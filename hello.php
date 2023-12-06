<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
$profile = CapabilityProfile::load("POS-5890");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name']) && isset($_POST['code']) && isset($_POST['kanwil'])) {
        $name = $_POST['name'];
        $code = $_POST['code'];
        $kanwil = $_POST['kanwil'];
        $response = array('status' => 'success', 'message' => 'Data received successfully');
		$connector = new WindowsPrintConnector("POS-80");
		$printer = new Printer($connector);
		$printer -> initialize();
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("46 Tahun BP Jamsostek\n");
		$printer -> text("Employee Gathering\n\n");
		$printer -> selectPrintMode(Printer::MODE_FONT_B);
		$printer -> setTextSize(2, 2);
		$printer -> text($name."\n");
		$printer -> text("KANWIL " . $kanwil."\n");
		$printer -> feed();
		$printer -> qrCode($code, Printer::QR_ECLEVEL_H, 8, Printer::QR_MODEL_1);
		$printer -> feed();
		$printer -> feed();
		$printer -> feed();
		$printer -> cut();
		$printer -> close();
        http_response_code(200);
        echo json_encode($response);
        
    } else {
        $response = array('status' => 'error', 'message' => 'Bad Request: Missing required parameters');
        http_response_code(400);
        echo json_encode($response);
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    http_response_code(400);
    echo json_encode($response);
}

?>