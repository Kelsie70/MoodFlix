<!DOCTYPE>

<html>

    <head>

        <title>Moodflix</title>

        <link rel="stylesheet" href="style.css">

        <script src="https://d3js.org/d3.v4.min.js"></script>


    </head>

    <body>

        <h1>Moodflix</h1>


        <div id="align">
          <?php
          include("shared.php");
          $moviename = strtolower($_POST['name']);
          $db = new mysqli($servername, $username, $password, $dbname);
          $query = "select * from analysis where moviename='". $moviename ."';";

          if(!$rs = $db->query($query)){
              die('There was an error running the query [' . $db->error . ']');
          }

          $row_count = $rs->num_rows;
          //echo $row_count;

          if($row_count === 0){
            /* run tone analyzer.*/



            // first get review from NYT API
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $query = array(
              "api-key" => "1898a64074374290983801728b9aaf70",
              "query" => $moviename
            );
            curl_setopt($curl, CURLOPT_URL,
              "https://api.nytimes.com/svc/movies/v2/reviews/search.json" . "?" . http_build_query($query)
            );
            $results = json_decode(curl_exec($curl), true);
            $movieFound = false;
            $review;
            $index = 0;
           //print_r($results['results'][2]);
            $count = count($results['results']);
            for($i=0;$i<$count;$i++){
              $testMovie = strtolower($results['results'][$i]['display_title']);
              if($testMovie === $moviename){
                //echo 'hi';

                $review = $results['results'][$i]['summary_short'];
                if($review!==""){
                  $movieFound = true;
                  $index = $i;
                  break;
                }
              }
            }

            if(!$movieFound){
              exit("<h4>Movie not found!</h4>");
            }
            $movieURL = $results['results'][$index]['link']['url'];
          //echo '<form action="'. $results['results'][$index]['link']['url'].'"><input type="submit" value="Review"/></form>';
            curl_close($curl);

            // now call Tone Analyzer

            $username='f278e2b8-dbe4-4450-b25c-07bed15a12b5';
            $password='i31Qtqfxlccd';
            $data = json_encode(array('text' => $review));
            $URL='https://gateway.watsonplatform.net/tone-analyzer/api/v3/tone?version=2016-05-19';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$URL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = json_decode(curl_exec($ch));
            curl_close ($ch);
            $emotions = array();
            $categories = array();
            $run = false;
            for($i=0;$i<5;$i++){
              $category = strtolower($result->document_tone->tone_categories[0]->tones[$i]->tone_id);
              $categories[$i] = $category;
              $emotions[$category] = $result->document_tone->tone_categories[0]->tones[$i]->score * 100;

            }

            $query = "INSERT INTO analysis(". $categories[0] .", ". $categories[1] ." , ". $categories[2] ."
            , ". $categories[3] ." , ". $categories[4] .", moviename, url)
                         VALUES
                         ( ". $emotions[$categories[0]] .", ". $emotions[$categories[1]] .", ". $emotions[$categories[2]] .",
                       ". $emotions[$categories[3]] .", ". $emotions[$categories[4]] .", '". $moviename ."', '". $movieURL. "');";


            if(!$resultsSet = $db->query($query)){
                 die('There was an error running the query [' . $db->error . ']');
            } // inserted emotions and movies into database


          }
          ?>
                <h4>your Moodflix for <?php echo $moviename?> </h4>


                    <div id="icons">
                        <img class="imageOne image" src="pictures/joy.png"/>
                        <img class="imageTwo image" src="pictures/sadness.png"/>
                        <img class="imageThree image" src="pictures/disgust.png"/>
                        <img class="imageFour image" src="pictures/anger.png"/>
                        <img class="imageFive image" src="pictures/fear.png"/>
                    </div>
                    <div id="chart"></div>
                    <script type="text/javascript">




                       var w = 470;
                       var h = 600;
                       var barPadding = 20;

                       var svg = d3.select("#chart")
                                .append("svg")
                                .attr("width", w)
                                .attr("height", h);



                       var dataset = [<?php
                       $query = "SELECT joy, sadness, anger, fear, disgust, url from analysis
                       where moviename='" . $moviename . "';";
                       $movieURL="";
                       if(!$resultsSet = $db->query($query)){
                            die('There was an error running the query [' . $db->error . ']');
                       }
                       while($row = $resultsSet->fetch_assoc()){
                         echo $row['joy'] . ", " .
                         $row['sadness'] . ", " .
                         $row['disgust'] . ", " .
                         $row['anger'] . ", " .
                         $row['fear'];
                         $movieURL = $row['url'];
                         //echo $movieURL;

                       }?>
                     ];

                       //var colors = ['#0000b4','#0082ca','#0094ff','#0d4bcf','#0066AE'];
                        var colors = ['#FE4365', '#FC9D9A', '#F9CDAD', '#C8C8A9', '#83AF9B'];

                       //var colorScale = d3.scale.ordinal().range(colors);
                       var colorScale = d3.scaleOrdinal()
                       .range(['#AB83EF', '#82DAE5', '#9FCB85', '#D8807A', '#D5BA79']);

                       svg.selectAll("rect")
                        .data(dataset)
                        .enter()
                        .append("rect")
                        .attr("y", function(d, i) {
                            return i * (w / dataset.length);
                        })
                        .attr("x", function(d){
                           return 0;
                       })
                        .attr("height", w / dataset.length - barPadding)
                        .attr("width", function(d){
                           return d * 4;
                       })
                        //.attr("background-color", function(d, i){
                        //    return colorScale(i);
                       //})
                        .attr("fill", colorScale)
                        .attr("rx", 10);



                       svg.selectAll("text")
                        .data(dataset)
                        .enter()
                        .append("text")
                        .text(function(d){
                           return d + "%";
                        })
                        .attr("y", function(d, i) {
                           return (i * (w / dataset.length) + (w / dataset.length - barPadding) / 2) + 4;
                        })
                        .attr("x", function(d) {
                             return (d * 4) + 35;
                        })
                        .attr("font-family", "monospace")
                        .attr("font-size", "20px")
                        .attr("fill", "gray")
                        .attr("text-anchor", "middle");


                       /*
                       d3.select("body").selectAll("div")
                        .data(dataset)
                        .enter()
                        .append("div")
                        .attr("class", "bar")
                       .style("height", function(d) {
                        var barHeight = d * 5;  //Scale up by factor of 5
                        return barHeight + "px";
                        });
                       */


                    </script>

                    <form action="<?php echo $movieURL; ?>"><input type="submit" id="submit" value="Review"/></form>


        </div>







        <!-- <div id="reviewURL" style="display: inherit"> -->


        <!-- </div> -->
    </body>

    <footer>
        <a href="http://www.freepik.com/free-vector/whatsapp-emoji_904078.html"><h4>Designed by Freepik</h4></a>
    </footer>

</html>
