<html>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script>
        function clicked(){
            $.ajax({
                url: 'filecheck.php',
                 success: function(data){
                    alert(data);
                }
            });
        }
    </script>
    <body>
        <input type="button" onclick="clicked();" value="Save"> 
    </body>
</html>
