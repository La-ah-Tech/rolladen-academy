<?php
/**
 * Simple test script to verify Xdebug debugging works
 * Access this via: http://your-domain/moodle/debug_test.php
 */

echo "<h1>Xdebug Debug Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

if (extension_loaded('xdebug')) {
    echo "<p style='color: green;'>✅ Xdebug is loaded and ready!</p>";
    echo "<p>Xdebug Version: " . phpversion('xdebug') . "</p>";
    echo "<p>Mode: " . ini_get('xdebug.mode') . "</p>";
    echo "<p>Client Host: " . ini_get('xdebug.client_host') . "</p>";
    echo "<p>Client Port: " . ini_get('xdebug.client_port') . "</p>";
    
    // Test variables for debugging
    $test_array = [
        'name' => 'Moodle Debug Test',
        'version' => '4.4',
        'features' => ['debugging', 'profiling', 'tracing']
    ];
    
    $test_object = new stdClass();
    $test_object->message = "Set a breakpoint on the next line!";
    $test_object->timestamp = time();
    
    // This is where you can set a breakpoint in VS Code
    var_dump($test_array, $test_object);
    
    echo "<p style='color: blue;'>🔍 Set a breakpoint on the var_dump line above in VS Code</p>";
    echo "<p style='color: blue;'>🔍 Then refresh this page to trigger the debugger</p>";
    
} else {
    echo "<p style='color: red;'>❌ Xdebug is NOT loaded!</p>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Open this project in VS Code</li>";
echo "<li>Go to Run and Debug (Ctrl+Shift+D)</li>";
echo "<li>Select 'Listen for Xdebug' configuration</li>";
echo "<li>Click the play button to start listening</li>";
echo "<li>Set a breakpoint on line 25 (var_dump line)</li>";
echo "<li>Refresh this page in your browser</li>";
echo "<li>VS Code should break at your breakpoint!</li>";
echo "</ol>";
?>
