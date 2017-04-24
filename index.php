<!doctype html>
<html>
    <head>
        <title>Статистика</title>
        <link rel="stylesheet" href="css/style.css"/>
        <script src="//d3js.org/d3.v3.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="left_sidbar white">
            </div>
            <div class="main">
                <div class="main__center">
                    <div class="header black bold">
                        Общая статистика по сайту
                    </div>
                    <div class="content">
                        
                        <p>55</p>
                        <script>
                            var paragraphs = document.getElementsByTagName("p");
                            for (var i = 0; i < paragraphs.length; i++) {
                              var paragraph = paragraphs.item(i);
                              paragraph.style.setProperty("color", "white", null);
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
