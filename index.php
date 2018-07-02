<?php
$persona = ["persona"];
?>

<html>
    <header>
        <h1> Mydex API Test </h1>

    </header>

    <body>

        <?php

        // set up user variables
        $personaArray = array(
            "personaA"=>array(2963,"json","2963-1545","pocffmLEMNP35vxgL1jAOD54Mxq94Ebt","JBlpO8SMl0XA3NMh4Zv2o2pPJtQMB8Z3","field_ds_personal_details"),
            "personaB"=>array(2966,"json","2966-1545","t4TSU6WiNeuWH8zQx6DfX9k0FTvgRtul","JBlpO8SMl0XA3NMh4Zv2o2pPJtQMB8Z3","field_ds_personal_details"));

        // set url once persona is selected
        function getURL($persona, $personaArray){
            // set up API variables & url
            $user_id = $personaArray[$persona][0];
            $response = $personaArray[$persona][1];
            $conn_id = $personaArray[$persona][2];
            $conn_key = $personaArray[$persona][3];
            $api_key = $personaArray[$persona][4];
            $data_set = $personaArray[$persona][5];

            $personaUrl = "https://sbx-api.mydex.org/api/pds/pds/{$user_id}.{$response}?key={$conn_key}&api_key={$api_key}&con_id={$conn_id}&source_type=connection&dataset={$data_set}";
            return $personaUrl;
        }

        // get API data once persona is selected
        function getAPI($url){

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
            return $dataArray;
        }

        // detect if persona selected
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["persona"])){
            // set variable to selected persona
            $personaBoo = True;
            $persona = $_POST["persona"];
            $url = getURL($persona,$personaArray);
            $mydexArray = getAPI($url);

        }
        elseif (isset($_POST["persona"])) {
            $persona = $_POST["persona"];
            }
        else {
            // set variable to default persona
            $persona = "personaA";
        }

        $mydexArray = array();
        $url = getURL($persona,$personaArray);
        $mydexArray = getAPI($url);

        ?>

        <h2> Select persona and then press Submit to retrieve PDS data </h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="radio" name="persona" value="personaA" checked="checked">personaA</input>
            <input type="radio" name="persona" value="personaB">personaB</input>
            <input type="submit" name="submitPersona" value="Submit">
        </form>


        <br>

        <!-- Create table & populate with data from cURL response -->
        <table cellpadding="15px" frame="border" rules="cols">
            <thead>
                <th>Persona Selected</th>
                <?php foreach ($mydexArray["field_ds_personal_details"]["instance_0"] as $key => $value): ?>
                <th><?php
                    $keystart = strrpos($key, "_")+1;
                    $key = substr($key,$keystart,strlen($key));
                    echo ucwords($key) ?></th>
                <?php endforeach ?>
            </thead>
            <tc>
                <td> <?php echo $persona ?></td>
                <?php foreach ($mydexArray["field_ds_personal_details"]["instance_0"] as $field): ?>
                <td> <?php echo ucfirst($field["value"]) ?></td>
            </tc>
                <?php endforeach ?>

        </table>

        <br>



        <h2> Update title and/or first name and then press Submit to save this to the PDS </h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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



        <?php

        function updateResponses($url, $dataArray)
        {

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
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $data = curl_exec($ch);
            $responseArray = json_decode($data);

            curl_close($ch);

            print_r($responseArray);

        }

        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']))
        {


            //updateResponses($url, $mydexArray);
            echo "Update submitted";
            //$persona = $_POST["persona"];
            echo $persona;
            $url = getURL($persona,$personaArray);
            $mydexArray = getAPI($url);

        }

        ?>

    </body>
</html>