<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Mars Rover</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="http://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>		
	</head>
	<body>
	<div class="container" style="margin-top:1%;">
		<div class="row">
			<div class="col-md-12 text-center">
				<h4>Upload Rover Command File or Drive Rover by Entering Commands on the right.</h4>
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<form name="f_rover" id="f_rover" method="POST" action="">
						<fieldset style="margin-bottom:10px;">
							<div class="col-sm-12">
								<label for="f_file">Upload Rover Command File</label>
								<input type="file" class="form-control" id="f_file">
							</div>
						</fieldset>
						<div class="col-sm-12">
							<button type="button" class="btn btn-info btn-md pull-right" onclick="processFile();return false;">Process File</button>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<fieldset>
						<div class="col-md-4" style="margin-bottom:10px;">
							<label for="f_gridsize">Grid (Zone) Size</label>
							<input type="text" class="form-control" id="f_gridsize" placeholder="8 8" onchange="" onblur="$(this).val($(this).val().replace(/\s+/g, ',').replace(/\s+/g, ''));drawGrid(this.value);return false;">
						</div>
					</fieldset>	
					<fieldset style="margin-bottom:10px;">
						<div class="col-md-8">
							<label for="f_startpos">Rover Starting Location and Orientation</label>
							<input type="text" class="form-control" id="f_startpos" placeholder="1 2 E" onchange="" onblur="$(this).val($(this).val().replace(/\s+/g, ',').replace(/\s+/g, ''));setRover(this.value);return false;">
						</div>
					</fieldset>
					<fieldset style="margin-bottom:10px;">
						<div class="col-md-8">
							<label for="f_command">Rover Movement & Rotation Commands</label>
							<input type="text" class="form-control" id="f_command" placeholder="MMLMRMMRRMML" onblur="$(this).val( $(this).val().replace(/[^M|L|R]+/g, '') );">
						</div>
					</fieldset>
					<div class="col-md-8 text-right">
						<button type="button" class="btn btn-info btn-md" onclick="moveRover( document.getElementById('f_gridsize').value, document.getElementById('f_startpos').value, document.getElementById('f_command').value );return false;">Move Rover</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-3 col-md-offset-3">
				<label>Starting Position</label>
				<input type="text" class="form-control text-center" id="dv_start" disabled>
			</div>
			<div class="col-md-3">
				<label>End Position</label>
				<input type="text" class="form-control text-center" id="dv_end" disabled>
			</div>
			<div id="dv_grid" class="col-md-6 col-md-offset-3"></div>
		</div>
	</div>
	</body>
</html>
<script type="text/javascript">
	function processFile() {
		var file_data = $('#f_file').prop('files')[0];   
		var form_data = new FormData();                  
		form_data.append('file', file_data);
		$.ajax({
			url: 'process_file.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
			success: function(result){
				var arr = result.split("|");
				if (arr[0] == "success") {
					$('#f_gridsize').val(arr[1]);
					$('#f_startpos').val(arr[2]);
					$('#f_command').val(arr[3]);
					drawGrid(arr[1]);
					setRover(arr[2]);
					moveRover(arr[1], arr[2], arr[3]);
				}
            }
		});
	}

	function drawGrid(grid_val) {
		var grid = grid_val.split(",");
		var html = '<table cellspacing="1" cellpadding="10" border="0" style="width:100%;">'+
		'<tr><td colspan="12" style="text-align:center;padding:10px;">N</td></tr>'+
		'<tr>';
		for (i = 1; i <= grid[0]; i++) {
			html = html + '<td style=\"text-align:center;background:#f7f7f7;border:1px solid #eee;border-top:0px none;padding:10px;\">'+i+'</td>';
		}
		html = html + '<td style=\"text-align:center;background:#eee;border:1px solid #eee;border-top:0px none;border-right:0px none;padding:10px;\"></td>';
		html = html + '</tr>';

		var ctj = grid[1];
		var ctx = 0;
		for (i = 1; i <= grid[0] * grid[1]; i++) {
			ctx++;
			html = html + '<td class="cl_grid" id="'+ctx+'_'+ctj+'" style="text-align:center;color:#ccc;border:1px solid #eee;padding:14px 10px;">'+ctx+','+ctj+'</td>';
			if (i % grid[0] == 0) {
				html = html + '<td style=\"text-align:center;background:#f7f7f7;border:1px solid #eee;border-right:0px none;padding:10px;\">'+ctj+'</td>';
				ctx = 0;
				ctj--;
				html = html + '</tr><tr>';
			}
		}
		html = html + '<tr><td colspan="12" style="text-align:center;padding:10px;">S</td></tr>';
		html = html + '</table>';
		$('#dv_grid').empty().html(html);
	}
	
	function setRover(rover_pos) {
		$('.cl_grid').css({'background':'initial','border':'initial','color':'#ccc'});
		
		var pos_arr = rover_pos.split(",");
		var x = parseInt(pos_arr[0]);
		var y = parseInt(pos_arr[1]);
		var d = pos_arr[2];
		
		$('#'+x+'_'+y).css({"color":"initial","background":"lightblue"});
		switch(d) {
			case "N": $('#'+x+'_'+y).css("border-top","3px solid red"); break;
			case "E": $('#'+x+'_'+y).css("border-right","3px solid red"); break;
			case "S": $('#'+x+'_'+y).css("border-bottom","3px solid red"); break;
			case "W": $('#'+x+'_'+y).css("border-left","3px solid red"); break;
		}

		$('#dv_start').val(x+' '+y+' '+d);
	}
	
	function moveRover(grid_size, rover_start, command) {
		if (grid_size && rover_start && command) {
			$('.cl_grid').css({'background':'initial','border':'initial','color':'#ccc'});
			
			var grid = grid_size.split(",");
			var h_max = grid[0];
			var v_max = grid[1];
			
			var rover_pos = rover_start.split(",");
			var x = parseInt(rover_pos[0]);
			var y = parseInt(rover_pos[1]);
			var d = rover_pos[2];
			var command_arr = command.split('');
			
			$('#'+x+'_'+y).css({'background':'lightblue','border':'initial','color':'initial'});
			switch(d) {
				case "N": $('#'+x+'_'+y).css("border-top","3px solid red"); break;
				case "E": $('#'+x+'_'+y).css("border-right","3px solid red"); break;
				case "S": $('#'+x+'_'+y).css("border-bottom","3px solid red"); break;
				case "W": $('#'+x+'_'+y).css("border-left","3px solid red"); break;
			}
		
			for (i = 0; i < command_arr.length; i++) {
				switch(command_arr[i]) {
					case "M":
						switch(d) {
							case "N": if ( (y + 1) <= v_max ) { y = y + 1; } $('#'+x+'_'+y).css("background","#eee"); break;
							case "E": if ( (x + 1) <= h_max ) { x = x + 1; } $('#'+x+'_'+y).css("background","#eee"); break;
							case "S": if ( (y - 1) >= 1 ) { y = y - 1; } $('#'+x+'_'+y).css("background","#eee"); break;
							case "W": if ( (x - 1) >= 1 ) { x = x - 1; } $('#'+x+'_'+y).css("background","#eee"); break;
						}
					break;
					case "L":
						switch(d) {
							case "N": d = "W"; break;
							case "E": d = "N"; break;
							case "S": d = "E"; break;
							case "W": d = "S"; break;
						}
					break;
					case "R":
						switch(d) {
							case "N": d = "E"; break;
							case "E": d = "S"; break;
							case "S": d = "W"; break;
							case "W": d = "N"; break;
						}
					break;
				}
			}
			$('#'+x+'_'+y).css({"color":"initial","background":"lightgreen"});
			
			switch(d) {
				case "N": $('#'+x+'_'+y).css("border-top","3px solid red"); break;
				case "E": $('#'+x+'_'+y).css("border-right","3px solid red"); break;
				case "S": $('#'+x+'_'+y).css("border-bottom","3px solid red"); break;
				case "W": $('#'+x+'_'+y).css("border-left","3px solid red"); break;
			}
	
			$("#dv_end").val(x+' '+y+' '+d);
		}
	}
</script>
