<?php
// config/functions.php
// Utility functions used across the application

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function calculate_final_score($tugas, $uts, $uas) {
    // 30% tugas, 30% UTS, 40% UAS
    $final = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
    return round($final, 2);
}

function score_to_letter_and_weight($score) {
    if ($score >= 85) return ['A', 4.00];
    if ($score >= 80) return ['A-', 3.75];
    if ($score >= 75) return ['B+', 3.50];
    if ($score >= 70) return ['B', 3.00];
    if ($score >= 65) return ['B-', 2.75];
    if ($score >= 60) return ['C+', 2.50];
    if ($score >= 55) return ['C', 2.00];
    if ($score >= 40) return ['D', 1.00];
    return ['E', 0.00];
}

function calculate_ips($total_mutu_semester, $total_sks_semester) {
    if ($total_sks_semester == 0) return 0;
    return round($total_mutu_semester / $total_sks_semester, 2);
}

function calculate_ipk($total_mutu_all, $total_sks_all) {
    if ($total_sks_all == 0) return 0;
    return round($total_mutu_all / $total_sks_all, 2);
}

function get_current_dosen(mysqli $mysqli, int $userId): ?array {
    $stmt = $mysqli->prepare('SELECT id_dosen, nama_dosen FROM dosen WHERE id_user = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ?: null;
}

// CSRF helpers
function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

function verify_csrf($token) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
