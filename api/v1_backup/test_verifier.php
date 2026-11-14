<?php
require_once __DIR__ . '/helpers/attendance_security.php';
try {
    $cfg = attendanceConfig();
    $process = @proc_open([$cfg['python'], $cfg['verifier']],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, [
            'AITF_FACE_PYTHON_LIB' => $cfg['python_lib'],
            'AITF_FACE_MODELS' => $cfg['models']
        ], ['bypass_shell' => true, 'create_no_window' => true]);
    if (!is_resource($process)) {
        echo json_encode(['error' => 'proc_open failed']);
        exit;
    }
    fwrite($pipes[0], '{}');
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]); proc_close($process);
    echo json_encode(['out' => $out, 'err' => $err]);
} catch (Throwable $t) {
    echo json_encode(['exception' => $t->getMessage()]);
}
