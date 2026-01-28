<?php
echo "<div style='padding: 40px; background: linear-gradient(135s, #1e293b, #0f172a); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); color: #f8fafc; font-family: sans-serif; text-align: center;'>";
echo "<h2 style='color: #818cf8;'>WordPress Bridge Mock Output</h2>";
$theme = apply_filters('template', 'default');
$isFse = is_dir(ABSPATH . 'wp-content/themes/' . $theme . '/templates');
echo "<div style='display: inline-block; padding: 10px 20px; background: rgba(255,255,255,0.05); border-radius: 10px; margin-top: 20px;'>";
echo "<p>Theme Mode: <strong style='color: #c084fc;'>" . ($isFse ? 'Gutenberg (FSE)' : 'Legacy (Classic)') . "</strong></p>";
echo "<p>Active Theme: <strong style='color: #6366f1;'>" . $theme . "</strong></p>";
echo "<p>Status: <strong style='color: #4ade80;'>Rendered via PrestoWorld Bridge</strong></p>";
echo "</div>";
echo "<p style='margin-top: 30px; color: #94a3b8;'>This is a hybrid rendering proof-of-concept.</p>";
echo "</div>";
