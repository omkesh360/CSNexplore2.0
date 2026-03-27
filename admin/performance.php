<?php
$admin_page  = 'performance';
$admin_title = 'Performance | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Performance Manager</h2>
            <p class="text-xs text-slate-500 font-medium">Optimize system speed and cache settings</p>
        </div>
        <button onclick="loadPerformanceData()" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-all text-slate-600">
            <span class="material-symbols-outlined text-sm">refresh</span> Refresh
        </button>
    </div>
<!-- Performance Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-600 font-semibold">Cache Hit Rate</p>
            <span class="material-symbols-outlined text-primary text-2xl">speed</span>
        </div>
        <p id="stat-cache-hit" class="text-4xl font-black text-slate-900 mb-1">—%</p>
        <p class="text-sm text-slate-500">Last 24 hours</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-600 font-semibold">Avg Page Load</p>
            <span class="material-symbols-outlined text-primary text-2xl">schedule</span>
        </div>
        <p id="stat-page-load" class="text-4xl font-black text-slate-900 mb-1">—ms</p>
        <p class="text-sm text-slate-500">Average response time</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-600 font-semibold">Cache Size</p>
            <span class="material-symbols-outlined text-primary text-2xl">storage</span>
        </div>
        <p id="stat-cache-size" class="text-4xl font-black text-slate-900 mb-1">—MB</p>
        <p class="text-sm text-slate-500">/ 2000MB max</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-600 font-semibold">Images Optimized</p>
            <span class="material-symbols-outlined text-primary text-2xl">image</span>
        </div>
        <p id="stat-images" class="text-4xl font-black text-slate-900 mb-1">—</p>
        <p class="text-sm text-slate-500">Total optimized</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <button onclick="purgeAllCache()" class="flex items-center gap-2 px-4 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-medium text-sm transition-all">
            <span class="material-symbols-outlined">delete_sweep</span> Purge All Cache
        </button>
        <button onclick="purgePageCache()" class="flex items-center gap-2 px-4 py-3 bg-orange-50 text-orange-600 hover:bg-orange-100 rounded-xl font-medium text-sm transition-all">
            <span class="material-symbols-outlined">clear</span> Clear Page Cache
        </button>
        <button onclick="purgeQueryCache()" class="flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl font-medium text-sm transition-all">
            <span class="material-symbols-outlined">database</span> Clear Query Cache
        </button>
        <button onclick="preloadCache()" class="flex items-center gap-2 px-4 py-3 bg-green-50 text-green-600 hover:bg-green-100 rounded-xl font-medium text-sm transition-all">
            <span class="material-symbols-outlined">cloud_upload</span> Preload Cache
        </button>
        <button onclick="generateTestData()" class="flex items-center gap-2 px-4 py-3 bg-purple-50 text-purple-600 hover:bg-purple-100 rounded-xl font-medium text-sm transition-all">
            <span class="material-symbols-outlined">science</span> Generate Test Data
        </button>
    </div>
    <div id="action-loading" class="hidden mt-4 flex items-center gap-2 text-sm text-slate-600">
        <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
        <span id="action-status">Processing...</span>
    </div>
    <div id="action-progress" class="hidden mt-4 w-full bg-slate-100 rounded-full h-2 overflow-hidden">
        <div id="action-progress-bar" class="bg-primary h-full transition-all duration-300" style="width:0%"></div>
    </div>
</div>

<!-- Feature Toggles -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Feature Settings</h2>
    <div class="space-y-3">
        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div>
                <p class="font-medium text-sm">Page Caching</p>
                <p class="text-xs text-slate-500">Cache full pages for faster delivery</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-cache" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>

        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div>
                <p class="font-medium text-sm">Image Optimization</p>
                <p class="text-xs text-slate-500">Compress and convert images to WebP</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-image" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>

        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div>
                <p class="font-medium text-sm">Asset Minification</p>
                <p class="text-xs text-slate-500">Minify CSS, JS, and HTML files</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-assets" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>

        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div>
                <p class="font-medium text-sm">Query Caching</p>
                <p class="text-xs text-slate-500">Cache database query results</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-query" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>

        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div>
                <p class="font-medium text-sm">Lazy Loading</p>
                <p class="text-xs text-slate-500">Defer image loading until viewport</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-lazy" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>
    </div>
    <button onclick="saveFeatureSettings()" class="mt-4 px-6 py-2 bg-primary text-white rounded-xl font-medium hover:bg-primary-dark transition-all">
        Save Feature Settings
    </button>
</div>

<!-- Configuration Settings -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Configuration</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Cache TTL (seconds)</label>
            <input type="number" id="config-cache-ttl" min="60" max="86400" value="3600" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Image Quality (1-100)</label>
            <input type="number" id="config-image-quality" min="1" max="100" value="75" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Max Cache Size (MB)</label>
            <input type="number" id="config-cache-size" min="100" max="10000" value="2000" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Query Cache TTL (seconds)</label>
            <input type="number" id="config-query-ttl" min="60" max="86400" value="3600" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>
    </div>
    <button onclick="saveConfiguration()" class="mt-4 px-6 py-2 bg-primary text-white rounded-xl font-medium hover:bg-primary-dark transition-all">
        Save Configuration
    </button>
</div>

<!-- Performance Metrics -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Performance Metrics</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Metric</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Value</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Status</th>
                </tr>
            </thead>
            <tbody id="metrics-table"></tbody>
        </table>
    </div>
</div>

<!-- Slow Queries -->
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h2 class="text-base font-bold mb-4">Slow Queries (Last 24h)</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Query</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Time (ms)</th>
                    <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500">Detected</th>
                </tr>
            </thead>
            <tbody id="slow-queries-table"></tbody>
        </table>
    </div>
</div>
</div>

<?php
$extra_js = <<<'JS'
<script>
// Load performance data
async function loadPerformanceData() {
    try {
        console.log('Loading performance data from: ../php/api/performance.php');
        var data = await api('../php/api/performance.php');
        console.log('Data received:', data);
        
        if (!data) {
            console.error('No data returned from API or invalid JSON');
            showAdminToast('Unable to load performance data', 'error');
            return;
        }

        // Update stats
        if (document.getElementById('stat-cache-hit')) {
            document.getElementById('stat-cache-hit').textContent = (data.cache_hit_rate || 0).toFixed(1) + '%';
        }
        if (document.getElementById('stat-page-load')) {
            document.getElementById('stat-page-load').textContent = (data.avg_page_load || 0).toFixed(0) + 'ms';
        }
        if (document.getElementById('stat-cache-size')) {
            document.getElementById('stat-cache-size').textContent = (data.cache_size_mb || 0).toFixed(1);
        }
        if (document.getElementById('stat-images')) {
            document.getElementById('stat-images').textContent = data.images_optimized || 0;
        }

        // Load feature toggles
        if (data.features) {
            if (document.getElementById('toggle-cache')) document.getElementById('toggle-cache').checked = data.features.cache?.enabled ?? true;
            if (document.getElementById('toggle-image')) document.getElementById('toggle-image').checked = data.features.image?.enabled ?? true;
            if (document.getElementById('toggle-assets')) document.getElementById('toggle-assets').checked = data.features.assets?.enabled ?? true;
            if (document.getElementById('toggle-query')) document.getElementById('toggle-query').checked = data.features.query_cache?.enabled ?? true;
            if (document.getElementById('toggle-lazy')) document.getElementById('toggle-lazy').checked = data.features.lazy_loading?.enabled ?? true;
        }

        // Load configuration
        if (data.config) {
            if (document.getElementById('config-cache-ttl')) document.getElementById('config-cache-ttl').value = data.config.cache?.ttl || 3600;
            if (document.getElementById('config-image-quality')) document.getElementById('config-image-quality').value = data.config.image?.quality || 75;
            if (document.getElementById('config-cache-size')) document.getElementById('config-cache-size').value = data.config.cache?.max_size_mb || 500;
            if (document.getElementById('config-query-ttl')) document.getElementById('config-query-ttl').value = data.config.query_cache?.ttl || 3600;
        }

        // Load metrics
        var metricsTable = document.getElementById('metrics-table');
        if (metricsTable) {
            var metricsHtml = '';
            if (data.metrics) {
                for (var key in data.metrics) {
                    var metric = data.metrics[key];
                    var statusColor = metric.status === 'good' ? 'bg-green-100 text-green-700' : metric.status === 'warning' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700';
                    metricsHtml += '<tr class="border-b border-slate-50 hover:bg-slate-50">' +
                        '<td class="py-2.5 px-3 font-medium">' + escHtml(metric.label) + '</td>' +
                        '<td class="py-2.5 px-3 text-slate-600">' + escHtml(metric.value) + '</td>' +
                        '<td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold ' + statusColor + '">' + escHtml(metric.status) + '</span></td>' +
                    '</tr>';
                }
            }
            metricsTable.innerHTML = metricsHtml || '<tr><td colspan="3" class="text-center py-8 text-slate-400 text-sm">No metrics available</td></tr>';
        }

        // Load slow queries
        var queriesTable = document.getElementById('slow-queries-table');
        if (queriesTable) {
            var queriesHtml = '';
            if (data.slow_queries && data.slow_queries.length) {
                data.slow_queries.forEach(function(q) {
                    queriesHtml += '<tr class="border-b border-slate-50 hover:bg-slate-50">' +
                        '<td class="py-2.5 px-3 font-mono text-xs text-slate-600 max-w-md truncate" title="' + escHtml(q.query) + '">' + escHtml(q.query.substring(0, 50)) + '...</td>' +
                        '<td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">' + q.time.toFixed(0) + 'ms</span></td>' +
                        '<td class="py-2.5 px-3 text-slate-500 text-xs">' + escHtml(q.detected) + '</td>' +
                    '</tr>';
                });
            }
            queriesTable.innerHTML = queriesHtml || '<tr><td colspan="3" class="text-center py-8 text-slate-400 text-sm">No slow queries detected</td></tr>';
        }
    } catch (error) {
        console.error('Error loading performance data:', error);
        showAdminToast('Error: ' + error.message, 'error');
    }
}

function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Purge all cache
async function purgeAllCache() {
    if (!confirm('Are you sure you want to purge ALL cache? This will clear page cache, query cache, and image cache.')) return;
    showLoading('Purging all cache...');
    var result = await api('../php/api/performance.php', { method: 'POST', body: JSON.stringify({ action: 'purge_all' }) });
    hideLoading();
    if (result && result.success) {
        showAdminToast('All cache purged successfully!', 'success');
        loadPerformanceData();
    } else {
        showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
    }
}

// Purge page cache
async function purgePageCache() {
    if (!confirm('Clear all page cache?')) return;
    showLoading('Clearing page cache...');
    var result = await api('../php/api/performance.php', { method: 'POST', body: JSON.stringify({ action: 'purge_page_cache' }) });
    hideLoading();
    if (result && result.success) {
        showAdminToast('Page cache cleared!', 'success');
        loadPerformanceData();
    } else {
        showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
    }
}

// Purge query cache
async function purgeQueryCache() {
    if (!confirm('Clear all query cache?')) return;
    showLoading('Clearing query cache...');
    var result = await api('../php/api/performance.php', { method: 'POST', body: JSON.stringify({ action: 'purge_query_cache' }) });
    hideLoading();
    if (result && result.success) {
        showAdminToast('Query cache cleared!', 'success');
        loadPerformanceData();
    } else {
        showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
    }
}

// Preload cache
async function preloadCache() {
    if (!confirm('Preload cache for popular pages?')) return;
    showLoading('Preloading cache...');
    var result = await api('../php/api/performance.php', { method: 'POST', body: JSON.stringify({ action: 'preload_cache' }) });
    hideLoading();
    if (result && result.success) {
        showAdminToast('Cache preloaded successfully!', 'success');
        loadPerformanceData();
    } else {
        showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
    }
}

// Generate test data
async function generateTestData() {
    if (!confirm('Generate test cache and performance data?')) return;
    showLoading('Generating test data...');
    try {
        var result = await api('../php/api/test-cache.php');
        hideLoading();
        if (result && result.success) {
            showAdminToast('Test data generated successfully!', 'success');
            loadPerformanceData();
        } else {
            showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        hideLoading();
        showAdminToast('Error: ' + error.message, 'error');
    }
}

// Toggle feature
async function toggleFeature(feature, enabled) {
    var result = await api('../php/api/performance.php', { 
        method: 'POST', 
        body: JSON.stringify({ action: 'toggle_feature', feature: feature, enabled: enabled }) 
    });
    if (result && !result.success) {
        showAdminToast('Error: ' + (result.error || 'Unknown error'), 'error');
    }
}

// Save feature settings
async function saveFeatureSettings() {
    var features = {
        cache: document.getElementById('toggle-cache').checked,
        image: document.getElementById('toggle-image').checked,
        assets: document.getElementById('toggle-assets').checked,
        query_cache: document.getElementById('toggle-query').checked,
        lazy_loading: document.getElementById('toggle-lazy').checked
    };
    
    showLoading('Saving feature settings...');
    
    var promises = [];
    for (var feature in features) {
        promises.push(api('../php/api/performance.php', { 
            method: 'POST', 
            body: JSON.stringify({ action: 'toggle_feature', feature: feature, enabled: features[feature] }) 
        }));
    }
    
    await Promise.all(promises);
    hideLoading();
    showAdminToast('Feature settings saved successfully!', 'success');
}

// Save configuration
async function saveConfiguration() {
    var config = {
        cache_ttl: parseInt(document.getElementById('config-cache-ttl').value),
        image_quality: parseInt(document.getElementById('config-image-quality').value),
        cache_size_mb: parseInt(document.getElementById('config-cache-size').value),
        query_ttl: parseInt(document.getElementById('config-query-ttl').value)
    };
    showLoading('Saving configuration...');
    var result = await api('../php/api/performance.php', { 
        method: 'POST', 
        body: JSON.stringify({ action: 'save_config', config: config }) 
    });
    hideLoading();
    if (result && result.success) {
        showAdminToast('Configuration saved!', 'success');
    } else {
        showAdminToast('Error: ' + (result?.error || 'Unknown error'), 'error');
    }
}

// Loading helpers
function showLoading(msg) {
    document.getElementById('action-loading').classList.remove('hidden');
    document.getElementById('action-status').textContent = msg;
    document.getElementById('action-progress').classList.remove('hidden');
    animateProgress();
}

function hideLoading() {
    document.getElementById('action-loading').classList.add('hidden');
    document.getElementById('action-progress').classList.add('hidden');
    document.getElementById('action-progress-bar').style.width = '0%';
}

function animateProgress() {
    var bar = document.getElementById('action-progress-bar');
    var width = 0;
    var interval = setInterval(function() {
        width += Math.random() * 30;
        if (width > 90) width = 90;
        bar.style.width = width + '%';
        if (width >= 90) clearInterval(interval);
    }, 200);
}

// Load data on page load - wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing performance page...');
        initializeToggles();
        loadPerformanceData();
        setInterval(loadPerformanceData, 30000);
    });
} else {
    console.log('DOM already loaded, initializing performance page...');
    initializeToggles();
    loadPerformanceData();
    setInterval(loadPerformanceData, 30000);
}

// Initialize toggle event listeners
function initializeToggles() {
    console.log('Initializing toggle event listeners...');
    
    // Add change event listeners to all toggles
    var toggles = ['toggle-cache', 'toggle-image', 'toggle-assets', 'toggle-query', 'toggle-lazy'];
    toggles.forEach(function(id) {
        var toggle = document.getElementById(id);
        if (toggle) {
            toggle.addEventListener('change', function() {
                var feature = id.replace('toggle-', '').replace('-', '_');
                console.log('Toggle changed:', feature, '=', this.checked);
                showAdminToast('Feature ' + feature + ' ' + (this.checked ? 'enabled' : 'disabled'), 'success');
            });
        }
    });
}
</script>
JS;
require 'admin-footer.php';
?>
