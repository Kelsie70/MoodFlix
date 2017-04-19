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
                <h4>your Moodflix for <?php echo $_POST["name"]; ?></h4>
            
                    <div id="icons">
                        <img class="imageOne image" src="pictures/joy.png"/>
                        <img class="imageTwo image" src="pictures/sadness.png"/>
                        <img class="imageThree image" src="pictures/disgust.png"/>
                        <img class="imageFour image" src="pictures/anger.png"/>
                        <img class="imageFive image" src="pictures/fear.png"/>
                    </div>
                    <div id="chart"></div>
                
      
        </div>
        
       
        
        
        <script type="text/javascript">
           
           
           
           var w = 470;
           var h = 600;
           var barPadding = 20;
           
           var svg = d3.select("#chart")
                    .append("svg")
                    .attr("width", w)
                    .attr("height", h);
           
                    
           
           var dataset = [ 100, 40, 20, 50, 10];
           
           //var colors = ['#0000b4','#0082ca','#0094ff','#0d4bcf','#0066AE'];
            var colors = ['#FE4365', '#FC9D9A', '#F9CDAD', '#C8C8A9', '#83AF9B'];

           //var colorScale = d3.scale.ordinal().range(colors);
           var colorScale = d3.scaleOrdinal()
            //.range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56"]);
            .range(['#FE4365', '#FC9D9A', '#F9CDAD', '#C8C8A9', '#83AF9B']);
           
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
        
    </body>
    
    <footer>
        <a href="http://www.freepik.com/free-vector/whatsapp-emoji_904078.html"><h4>Designed by Freepik</h4></a>
    </footer>

</html>