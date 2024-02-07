<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
$profile = CapabilityProfile::load("POS-5890");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['customer_qrcode'])) {
        $name = $_POST['customer_name'];
		$id = $_POST['id'];
		$id = sprintf("%04d", $id);
		$cname = $_POST['customer_childname'];
        $code = $_POST['customer_qrcode'];
        $response = array('status' => 'success', 'message' => 'Data received successfully');
		$connector = new NetworkPrintConnector("192.168.0.31",9100);

		//$connector = new WindowsPrintConnector("POS-80");
		$printer = new Printer($connector);
		$printer -> initialize();
		// PARENT SHEET
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("Kidzania X Danamon\n\n");
		$printer -> selectPrintMode(Printer::MODE_FONT_B);
		$printer -> setTextSize(2, 2);
		$printer -> text($id."\n");
		$printer -> text("ORANG TUA :\n");
		$printer -> text($name."\n");
		$printer -> feed();
		$printer -> qrCode($code, Printer::QR_ECLEVEL_H, 8, Printer::QR_MODEL_1);
		$printer -> feed();
		$printer -> cut();
		//CHILD SHEET
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("Kidzania X Danamon\n\n");
		$printer -> selectPrintMode(Printer::MODE_FONT_B);
		$printer -> setTextSize(2, 2);
		$printer -> text($id."\n");
		$printer -> text("ANAK :\n");
		$printer -> text($cname."\n");
		$printer -> feed();
		$printer -> qrCode($code, Printer::QR_ECLEVEL_H, 8, Printer::QR_MODEL_1);
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