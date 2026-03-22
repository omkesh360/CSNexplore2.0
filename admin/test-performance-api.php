<!DOCTYPE html>
<html>
<head>
    <title>Performance API Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .test { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border: 2px solid #ddd; }
        .pass { border-color: #4caf50; }
        .fail { border-color: #f44336; }
        pre { background: #f9f9f9; padding: 10px; overflow-x: auto; }
        button { padding: 10px 20px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1976d2; }
    </style>
</head>
<body>
    <h1>Performance API Test</h1>
    <button onclick="runTests()">Run Tests</button>
    <div id="results"></div>

    <script>
        async function runTests() {
            const results = document.getElementById('results');
            results.innerHTML = '<p>Running tests...</p>';
            
            const tests = [];
            
            // Test 1: API GET request
            try {
                console.log('Test 1: Fetching performance data...');
                const response = await fetch('../php/api/performance.php');
                const contentType = response.headers.get('content-type');
                const data = await response.json();
                
                tests.push({
                    name: 'GET /php/api/performance.php',
                    pass: response.ok && contentType.includes('application/json'),
                    details: `Status: ${response.status}, Content-Type: ${contentType}`,
                    data: JSON.stringify(data, null, 2)
                });
            } catch (error) {
                tests.push({
                    name: 'GET /php/api/performance.php',
                    pass: false,
                    details: `Error: ${error.message}`
                });
            }
            
            // Test 2: Check data structure
            try {
                const response = await fetch('../php/api/performance.php');
                const data = await response.json();
                
                const hasRequiredFields = 
                    data.hasOwnProperty('cache_hit_rate') &&
                    data.hasOwnProperty('avg_page_load') &&
                    data.hasOwnProperty('cache_size_mb') &&
                    data.hasOwnProperty('images_optimized') &&
                    data.hasOwnProperty('features') &&
                    data.hasOwnProperty('config') &&
                    data.hasOwnProperty('metrics');
                
                tests.push({
                    name: 'Data Structure Validation',
                    pass: hasRequiredFields,
                    details: hasRequiredFields ? 'All required fields present' : 'Missing required fields',
                    data: `Fields: ${Object.keys(data).join(', ')}`
                });
            } catch (error) {
                tests.push({
                    name: 'Data Structure Validation',
                    pass: false,
                    details: `Error: ${error.message}`
                });
            }
            
            // Test 3: POST request (toggle feature)
            try {
                const response = await fetch('../php/api/performance.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle_feature', feature: 'cache', enabled: true })
                });
                const data = await response.json();
                
                tests.push({
                    name: 'POST toggle_feature',
                    pass: response.ok && data.success === true,
                    details: `Status: ${response.status}`,
                    data: JSON.stringify(data, null, 2)
                });
            } catch (error) {
                tests.push({
                    name: 'POST toggle_feature',
                    pass: false,
                    details: `Error: ${error.message}`
                });
            }
            
            // Render results
            results.innerHTML = tests.map(test => `
                <div class="test ${test.pass ? 'pass' : 'fail'}">
                    <h3>${test.pass ? '✓' : '✗'} ${test.name}</h3>
                    <p><strong>Status:</strong> ${test.pass ? 'PASS' : 'FAIL'}</p>
                    <p><strong>Details:</strong> ${test.details}</p>
                    ${test.data ? `<pre>${test.data}</pre>` : ''}
                </div>
            `).join('');
        }
    </script>
</body>
</html>
