// Warga Management dengan Bootstrap Compatibility
class WargaManager {
    constructor() {
        this.searchTimeout = null;
        this.currentSearch = '';
        this.init();
    }

    init() {
        this.cacheElements();
        this.bindEvents();
        this.initComponents();
    }

    cacheElements() {
        // Search & Filter Elements
        this.$searchInput = $('#searchWarga');
        this.$clearSearch = $('#clearSearch');
        this.$filterKK = $('#filterKK');
        
        // Table & Content Elements
        this.$tableWarga = $('#tableWarga');
        this.$tableContainer = $('.table-responsive');
        
        // Status Elements
        this.$loadingIndicator = $('#loadingIndicator');
        this.$resultsInfo = $('#resultsInfo');
        this.$resultsCount = $('#resultsCount');
        this.$searchTerm = $('#searchTerm');
        this.$emptyState = $('#emptyState');
        this.$emptyMessage = $('#emptyMessage');
        
        // Routes & CSRF
        this.routes = {
            search: this.$searchInput.data('search-url') || '/warga/search-ajax',
            index: '/warga'
        };
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
    }

    bindEvents() {
        // Search Events
        this.$searchInput.on('input', this.handleSearch.bind(this));
        this.$clearSearch.on('click', this.clearSearch.bind(this));
        this.$filterKK.on('change', this.handleKKFilter.bind(this));
        
        // Keyboard Events
        $(document).on('keydown', this.handleKeyboard.bind(this));
    }

    initComponents() {
        this.initTooltips();
        this.initFamilyGroups();
        this.attachDeleteHandlers();
        this.focusSearch();
    }

    // ===== SEARCH FUNCTIONALITY =====
    handleSearch(e) {
        const query = e.target.value.trim();
        this.currentSearch = query;
        
        clearTimeout(this.searchTimeout);
        
        this.hideEmptyState();
        
        if (!query) {
            this.hideResultsInfo();
            this.loadOriginalData();
            return;
        }
        
        this.showLoading();
        this.hideResultsInfo();
        
        this.searchTimeout = setTimeout(() => {
            this.executeSearch(query);
        }, 500);
    }

    async executeSearch(query) {
        try {
            const response = await $.get(this.routes.search, { search: query });
            this.updateTable(response.html);
            this.updateSearchInfo(query);
            this.hideLoading();
        } catch (error) {
            console.error('Search error:', error);
            this.hideLoading();
            this.showAlert('Terjadi kesalahan saat mencari data!', 'danger');
        }
    }

    updateTable(html) {
        this.$tableWarga.html(html).addClass('fade-in');
        this.initTooltips();
        this.initFamilyGroups();
        this.attachDeleteHandlers();
        
        setTimeout(() => {
            this.$tableWarga.removeClass('fade-in');
        }, 500);
    }

    updateSearchInfo(query) {
        const rowCount = this.countVisibleRows();
        
        if (rowCount > 0) {
            this.$resultsCount.text(rowCount);
            this.$searchTerm.text(query);
            this.showResultsInfo();
            this.hideEmptyState();
        } else {
            this.showEmptyState(`Tidak ditemukan data warga untuk: "${query}"`);
            this.hideResultsInfo();
        }
    }

    countVisibleRows() {
        return this.$tableWarga.find('tr').not('.d-none').length - 
               this.$tableWarga.find('tr.text-center').length;
    }

    // ===== DATA MANAGEMENT =====
    async loadOriginalData() {
        try {
            const response = await $.get(this.routes.index);
            const tableContent = $(response).find('#tableWarga').html();
            if (tableContent) {
                this.$tableWarga.html(tableContent);
                this.initTooltips();
                this.initFamilyGroups();
                this.attachDeleteHandlers();
            }
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    handleKKFilter(e) {
        const kk = $(e.target).val();
        if (kk) {
            this.filterByKK(kk);
        } else {
            this.loadOriginalData();
        }
    }

    filterByKK(kk) {
        this.$tableWarga.find('tr').addClass('d-none');
        this.$tableWarga.find(`[data-family="${kk}"]`).removeClass('d-none');
        this.$tableWarga.find('.family-separator').removeClass('d-none');
    }

    clearSearch() {
        this.$searchInput.val('');
        this.currentSearch = '';
        this.hideResultsInfo();
        this.hideEmptyState();
        this.loadOriginalData();
        this.focusSearch();
    }

    // ===== FAMILY GROUPS =====
    initFamilyGroups() {
        $('.family-header').off('click').on('click', (e) => {
            this.toggleFamily($(e.currentTarget));
        });
    }

    toggleFamily($header) {
        const familyId = $header.data('family');
        const $members = $(`.family-member[data-family="${familyId}"]`);
        const $icon = $header.find('i');
        
        $members.toggleClass('d-none');
        
        if ($members.hasClass('d-none')) {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else {
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    }

    // ===== DELETE FUNCTIONALITY =====
    attachDeleteHandlers() {
        $('.delete-btn').off('click').on('click', (e) => {
            this.handleDeleteClick($(e.currentTarget));
        });
    }

    async handleDeleteClick($button) {
        const id = $button.data('id');
        const name = $button.data('name');
        
        if (!await this.confirmDelete(name)) return;
        
        this.disableButton($button);
        
        try {
            await this.deleteWarga(id);
            await this.refreshData();
            this.showAlert('Data berhasil dihapus!', 'success');
        } catch (error) {
            this.enableButton($button);
            this.showAlert('Gagal menghapus data!', 'danger');
        }
    }

    confirmDelete(name) {
        return new Promise((resolve) => {
            const confirmed = confirm(`Hapus data warga "${name}"?`);
            resolve(confirmed);
        });
    }

    async deleteWarga(id) {
        return await $.ajax({
            url: `/warga/${id}`,
            type: 'DELETE',
            data: { _token: this.csrfToken }
        });
    }

    async refreshData() {
        if (this.currentSearch) {
            await this.executeSearch(this.currentSearch);
        } else {
            await this.loadOriginalData();
        }
    }

    // ===== UI CONTROLS =====
    initTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
    }

    showLoading() {
        this.$loadingIndicator.fadeIn();
    }

    hideLoading() {
        this.$loadingIndicator.fadeOut();
    }

    showResultsInfo() {
        this.$resultsInfo.slideDown();
    }

    hideResultsInfo() {
        this.$resultsInfo.slideUp();
    }

    showEmptyState(message) {
        this.$emptyMessage.text(message);
        this.$emptyState.slideDown();
    }

    hideEmptyState() {
        this.$emptyState.slideUp();
    }

    focusSearch() {
        this.$searchInput.focus();
    }

    disableButton($button) {
        $button.prop('disabled', true)
               .html('<i class="fas fa-spinner fa-spin"></i>');
    }

    enableButton($button) {
        $button.prop('disabled', false)
               .html('<i class="fas fa-trash"></i>');
    }

    // ===== ALERTS & NOTIFICATIONS =====
    showAlert(message, type) {
        const alertClass = `alert-${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const $alert = $(`
            <div class="alert ${alertClass} alert-custom alert-dismissible fade show slide-down" role="alert">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.alert').alert('close');
        $('.container').prepend($alert);
        
        setTimeout(() => {
            $alert.alert('close');
        }, 5000);
    }

    // ===== KEYBOARD SHORTCUTS =====
    handleKeyboard(e) {
        // Ctrl+F untuk focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            this.focusSearch();
        }
        
        // Escape untuk clear search
        if (e.key === 'Escape') {
            this.clearSearch();
        }
    }
}

// ===== INITIALIZATION =====
$(document).ready(function() {
    // Initialize Warga Manager
    window.wargaApp = new WargaManager();
    
    // Global error handler
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        console.error('Ajax error:', thrownError);
    });
});