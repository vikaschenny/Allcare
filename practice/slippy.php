<html>
<head>
<style>
  ul#slippylist li {
    user-select: none;
    -webkit-user-select: none;
    border: 1px solid lightgrey;
    list-style: none;
    height: 25px;
    max-width: 200px;
    cursor: move;
    margin-top: -1px;
    margin-bottom: 0;
    padding: 5px;
    font-weight: bold;
    color: black;
}
    ul#slippylist li.slip-reordering {
        box-shadow: 0 2px 10px rgba(0,0,0,0.45);
    }
</style>
</head>
<body>
<ul id="slippylist">
    <li>Item 1</li>
    <li>Item 2</li>
    <li>Item 3</li>
  </ul>
     <script src="js/slip.js"></script>
  <script>
    var list = document.getElementById('slippylist');
    new Slip(list);
    list.addEventListener('slip:reorder', function(e) {
    e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
  });
  </script>
</body>
</html>