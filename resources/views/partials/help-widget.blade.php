<!-- Context-Aware Help Widget -->
<div id="helpWidget" class="help-widget">
    <!-- Floating Help Button -->
    <button id="helpToggleBtn" class="help-widget-btn" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="Need help?">
        <i class="bi bi-question-circle-fill"></i>
    </button>
    
    <!-- Help Panel -->
    <div id="helpPanel" class="help-widget-panel">
        <div class="help-widget-header">
            <h5>Quick Help</h5>
            <button type="button" class="btn-close btn-close-white" onclick="toggleHelpWidget()"></button>
        </div>
        
        <div class="help-widget-search">
            <div class="input-group">
                <input type="text" 
                       id="helpSearchInput" 
                       class="form-control" 
                       placeholder="Search help articles..."
                       onkeyup="searchHelp(event)">
                <button class="btn btn-primary" type="button" onclick="searchHelp()">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        
        <div class="help-widget-content">
            <!-- Context-specific help will be loaded here -->
            <div id="contextualHelp">
                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="bi bi-lightbulb me-2"></i>Suggested Articles
                    </h6>
                    <div id="suggestedArticles" class="help-articles-list">
                        <!-- Dynamically loaded based on current page -->
                    </div>
                </div>
                
                <div class="help-section mt-3">
                    <h6 class="help-section-title">
                        <i class="bi bi-star me-2"></i>Popular Topics
                    </h6>
                    <div id="popularArticles" class="help-articles-list">
                        <!-- Popular articles will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Search Results -->
            <div id="searchResults" style="display: none;">
                <div class="help-section">
                    <h6 class="help-section-title">
                        <i class="bi bi-search me-2"></i>Search Results
                    </h6>
                    <div id="searchResultsList" class="help-articles-list">
                        <!-- Search results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="help-widget-footer">
            <a href="{{ route('kb.index') }}" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-book me-2"></i>View Full Knowledge Base
            </a>
            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('onboarding.start') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                <i class="bi bi-rocket me-2"></i>Restart Setup Guide
            </a>
            @endif
        </div>
    </div>
</div>

<style>
.help-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
}

.help-widget-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    50% {
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.6);
    }
    100% {
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
}

.help-widget-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.help-widget-btn.active {
    background: linear-gradient(135deg, #764ba2 0%, #f093fb 100%);
    animation: none;
}

.help-widget-panel {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 380px;
    max-height: 600px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.help-widget-panel.show {
    display: flex;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.help-widget-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.help-widget-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.help-widget-search {
    padding: 15px;
    border-bottom: 1px solid #e5e5e5;
}

.help-widget-content {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    max-height: 400px;
}

.help-section-title {
    color: #667eea;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
}

.help-articles-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.help-article-item {
    padding: 10px;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.help-article-item:hover {
    background: #f8f9fa;
    border-color: #667eea;
    transform: translateX(5px);
}

.help-article-title {
    font-weight: 500;
    color: #333;
    margin-bottom: 3px;
    font-size: 14px;
}

.help-article-excerpt {
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}

.help-widget-footer {
    padding: 15px;
    border-top: 1px solid #e5e5e5;
    background: #f8f9fa;
}

.no-results {
    text-align: center;
    padding: 30px;
    color: #999;
}

.no-results i {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 10px;
}

/* Loading spinner */
.help-loading {
    text-align: center;
    padding: 30px;
}

.spinner-border {
    width: 2rem;
    height: 2rem;
    border-width: 0.25em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .help-widget-panel {
        width: calc(100vw - 40px);
        right: 20px;
        bottom: 90px;
    }
    
    .help-widget-btn {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}
</style>

<script>
let helpWidgetOpen = false;
let currentPage = window.location.pathname;

// Toggle help widget panel
function toggleHelpWidget() {
    const panel = document.getElementById('helpPanel');
    const btn = document.getElementById('helpToggleBtn');
    
    helpWidgetOpen = !helpWidgetOpen;
    
    if (helpWidgetOpen) {
        panel.classList.add('show');
        btn.classList.add('active');
        loadContextualHelp();
    } else {
        panel.classList.remove('show');
        btn.classList.remove('active');
        // Reset to contextual help when closing
        document.getElementById('contextualHelp').style.display = 'block';
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('helpSearchInput').value = '';
    }
}

// Load contextual help based on current page
function loadContextualHelp() {
    const suggestedContainer = document.getElementById('suggestedArticles');
    const popularContainer = document.getElementById('popularArticles');
    
    // Show loading
    suggestedContainer.innerHTML = '<div class="help-loading"><div class="spinner-border text-primary" role="status"></div></div>';
    popularContainer.innerHTML = '<div class="help-loading"><div class="spinner-border text-primary" role="status"></div></div>';
    
    // Fetch contextual articles
    fetch(`/kb/api/contextual?page=${encodeURIComponent(currentPage)}`)
        .then(response => response.json())
        .then(data => {
            // Display suggested articles
            if (data.suggested && data.suggested.length > 0) {
                suggestedContainer.innerHTML = data.suggested.map(article => `
                    <a href="/kb/article/${article.slug}" class="help-article-item" target="_blank">
                        <div class="help-article-title">${article.title}</div>
                        <div class="help-article-excerpt">${article.excerpt || 'Click to learn more...'}</div>
                    </a>
                `).join('');
            } else {
                suggestedContainer.innerHTML = '<div class="no-results"><i class="bi bi-info-circle"></i><p>No specific help articles for this page.</p></div>';
            }
            
            // Display popular articles
            if (data.popular && data.popular.length > 0) {
                popularContainer.innerHTML = data.popular.map(article => `
                    <a href="/kb/article/${article.slug}" class="help-article-item" target="_blank">
                        <div class="help-article-title">${article.title}</div>
                        <div class="help-article-excerpt">${article.excerpt || 'Click to learn more...'}</div>
                    </a>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading contextual help:', error);
            suggestedContainer.innerHTML = '<div class="no-results">Unable to load help articles.</div>';
            popularContainer.innerHTML = '';
        });
}

// Search help articles
function searchHelp(event) {
    if (event && event.type === 'keyup' && event.key !== 'Enter') {
        return; // Only search on Enter key
    }
    
    const searchTerm = document.getElementById('helpSearchInput').value.trim();
    
    if (!searchTerm) {
        // Show contextual help if search is empty
        document.getElementById('contextualHelp').style.display = 'block';
        document.getElementById('searchResults').style.display = 'none';
        return;
    }
    
    const resultsContainer = document.getElementById('searchResultsList');
    
    // Show loading
    document.getElementById('contextualHelp').style.display = 'none';
    document.getElementById('searchResults').style.display = 'block';
    resultsContainer.innerHTML = '<div class="help-loading"><div class="spinner-border text-primary" role="status"></div></div>';
    
    // Search articles
    fetch(`/kb/api/search?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.articles && data.articles.length > 0) {
                resultsContainer.innerHTML = data.articles.map(article => `
                    <a href="/kb/article/${article.slug}" class="help-article-item" target="_blank">
                        <div class="help-article-title">${article.title}</div>
                        <div class="help-article-excerpt">${article.excerpt || 'Click to learn more...'}</div>
                    </a>
                `).join('');
            } else {
                resultsContainer.innerHTML = '<div class="no-results"><i class="bi bi-search-x"></i><p>No articles found for "' + searchTerm + '"</p></div>';
            }
        })
        .catch(error => {
            console.error('Error searching help:', error);
            resultsContainer.innerHTML = '<div class="no-results">Error searching articles. Please try again.</div>';
        });
}

// Initialize help button click handler
document.addEventListener('DOMContentLoaded', function() {
    const helpBtn = document.getElementById('helpToggleBtn');
    if (helpBtn) {
        helpBtn.addEventListener('click', toggleHelpWidget);
        
        // Initialize tooltip
        new bootstrap.Tooltip(helpBtn);
    }
    
    // Track page context changes (for single-page apps)
    if (window.history && window.history.pushState) {
        const originalPushState = history.pushState;
        history.pushState = function() {
            originalPushState.apply(history, arguments);
            currentPage = window.location.pathname;
            if (helpWidgetOpen) {
                loadContextualHelp();
            }
        };
    }
});

// Close widget when clicking outside
document.addEventListener('click', function(event) {
    const widget = document.getElementById('helpWidget');
    const btn = document.getElementById('helpToggleBtn');
    
    if (helpWidgetOpen && !widget.contains(event.target) && event.target !== btn) {
        toggleHelpWidget();
    }
});
</script>