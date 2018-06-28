<!DOCTYPE html>
<html>
    <header>
        <p> Details returned from API </p>

    </header>

    <body>

        <?php

        // set up API variables & url
        $user_id = 2963;
        $response = "json";
        $conn_id = "2963-1545";
        $conn_key = "pocffmLEMNP35vxgL1jAOD54Mxq94Ebt";
        $api_key = "JBlpO8SMl0XA3NMh4Zv2o2pPJtQMB8Z3";
        $data_set = "field_ds_personal_details";

        $url = "https://sbx-api.mydex.org/api/pds/pds/{$user_id}.{$response}?key={$conn_key}&api_key={$api_key}&con_id={$conn_id}&source_type=connection&dataset={$data_set}";

        // set up response variables
        $title_response = $fname_response = "";

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // grab URL response
        $data = curl_exec($ch);

        // close cURL resource
        curl_close($ch);

        // turn URL response data into array
        $dataArray = (json_decode($data, true));


        function updateResponses($dataArray,$url){

            // retrieve responses
            if (empty($_POST['title'])) {
                $title_resp = $dataArray["field_ds_personal_details"]["instance_0"]["field_personal_title"]["value"];
            } else {
                $title_resp = $_POST['title'];
            }

            if (empty($_POST['firstname'])) {
                $fname_resp = $dataArray["field_ds_personal_details"]["instance_0"]["field_personal_fname"]["value"];
            } else {
                $fname_resp = $_POST['firstname'];
            }

            // update $array with new reponses
            $instance[0] = 0;
            $data = array();
            $data["field_ds_personal_details"][$instance[0]]["field_personal_title"] = $title_resp;
            $data["field_ds_personal_details"][$instance[0]]["field_personal_fname"] = $fname_resp;

            $data = (is_array($data)) ? http_build_query($data) : $data;


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $data = curl_exec($ch);
            $dataArray = json_decode($data);

            curl_close($ch);

            print_r($dataArray);

        }

        if(isset($_POST['submit']))
        {
            updateResponses($dataArray,$url);
        }


        ?>

        <br>

        <!-- Create table & populate with data from cURL response -->
        <table cellpadding="15px" frame="border" rules="cols">
            <thead>
                <?php foreach ($dataArray["field_ds_personal_details"]["instance_0"] as $key => $value): ?>
                <th><?php
                    $keystart = strrpos($key, "_")+1;
                    $key = substr($key,$keystart,strlen($key));
                    echo ucwords($key) ?></th>
                <?php endforeach ?>
            </thead>
            <tc>
                <?php foreach ($dataArray["field_ds_personal_details"]["instance_0"] as $field): ?>
                <td> <?php echo ucfirst($field["value"]) ?></td>
            </tc>
                <?php endforeach ?>

        </table>

        <br>
        <p> Use the below drop down list to select the title and/or update your first name. Then press the Submit button to save this to the PDS. </p>

        <form method="post" action="index2.php">
            Select your Title :
            <select name="title" value="">
                <option value=""></option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
            </select>
            <br><br>
            Enter your First Name :
            <input name="firstname" type="text" value="">
            <br><br>
            <input type="submit" value="Submit" name="submit">
        </form>

        <br>

    </body>
</html>