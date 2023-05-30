<?php
//error_reporting(E_ALL);



require("helpers/server_response.php");

$request = new ClientRequest();
$dataSource = new DataSource("data.json");
$response = new ServerResponse($request, $dataSource);


$response->process();


function GET(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);


    $id = $request->get['id'] ?? false;
    $hwid = $request->get['hwid'] ?? 'hw1';

    $output = array(
        "cwid" => $id,
        "hwid" => $hwid,
        "grade" => ""
    );

    if ($id != false) {
        $cwid = $id;
        foreach ($data as $student) {
            if (array_key_exists("cwid", $student) && $student["cwid"] == $cwid) {
                $output['grade'] = $student['grades'][$hwid];
                break;
            }
        }
        $response->status = "OK";
    } else {
        $response->status = "ERROR - DATA NOT FOUND";
    }

    $response->outputJSON($output);
}

function PUT(ClientRequest $request, DataSource $dataSource, ServerResponse $response)
{
    $data = $dataSource->JSON(true);

    $cwid = $request->put['cwid'] ?? false;
    $hwid = $request->put['hwid'] ?? false;
    $pct = $request->put['grade'] ?? "";

    $target = 0;


    foreach ($data as $key => $student) {
        if ($student['cwid'] == $cwid) {
            $target = $key;
        }
    }

    $data[$target]['grades'][$hwid] = $pct;

    $newJson = json_encode($data);

    file_put_contents($dataSource->writePath, $newJson);

    
    $output = array(
        "cwid"=>$cwid,
        "hwid"=>$hwid,
        "grade"=>$pct
    );

    $response->status = "OK";
    $response->outputJSON($output);
}
