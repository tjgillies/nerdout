	<form name="classes_basic" method="post" id="classes_basic" action="<?= base_url() ?>api/nerdout/create_checkin" enctype="multipart/form-data">
	
		<h3>Title</h3>
		<p><input type="text" name="title" value="" id="title" class="input_full" /></p>
		    
		<input type="submit" name="publish" value="Continue" />
	
	</form>
	
<script type="text/javascript">
$(document).ready(function()
{
	// Write Article
	$("#classes_basic").bind("submit", function(eve)
	{
		eve.preventDefault();

		// Validation	
		
		var class_data = $('#classes_basic').serializeArray();
		class_data.push({'name':'source','value':'website'});

		$(this).oauthAjax(
		{
			oauth 		: user_data,
			url			: $(this).attr('ACTION'),
			type		: 'POST',
			dataType	: 'json',
			data		: class_data,
	  		success		: function(result)
	  		{
				$('html, body').animate({scrollTop:0});
				$('#content_message').notify({scroll:true,status:result.status,message:result.message});
		 	}
		});
		
	});	
});
</script>