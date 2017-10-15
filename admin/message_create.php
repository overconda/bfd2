<!DOCTYPE html>
<html language="en">
  <head>
    <meta charset="utf-8">
    <title>Create Message</title>

    <!-- include libraries(jQuery, bootstrap) -->
<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>

<!-- include summernote css/js-->
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css" rel="stylesheet">
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.js"></script>

<style>
.container div[class^="col-md"]{
  padding: 4px;
}
</style>
  </head>
  <body>
<form method="post" action="_create_message.php" id="frm">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <h3>Creat Message</h3>
      </div>
    </div>

  <div class="row">
    <div class="col-md-2">To</div>
    <div class="col-md-8"><input type=text name="to" id="to" class="form-control"></div>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2"><label><input type="checkbox" name="boardcase" /> Broadcast to all users</label></div>
  </div>
  <div class="row">
    <div class="col-md-2">Subject</div>
    <div class="col-md-8"><input type=text name="subject" class="form-control"></div>
  </div>
  <div class="row">
    <div class="col-md-2">Msg</div>
    <div class="col-md-8">
      <div id="summernote"></div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <input type="hidden" name="msg" id='msg' />
      <button type="submit" class="btn btn-info" id="submit">Send</button>
    </div>
  </div>
</div>
</form>
  </body>
  <script>
  $(document).ready(function(){
    $(document).ready(function() {
      $('#summernote').summernote({
        placeholder: 'Type message to send to user.',
        height:160
      });
    });



    $('#frm').submit(function(){
      var msg = $('#summernote').summernote('code');
      console.log(msg);
      $('#msg').val(msg);
    });
  });
  </script>
</html>
