<title>Bazarnao :: Referral Code Page</title>
<style>

body{
	width:100%;
	height:100%;
	
}
.contain{
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
	background: #a9427f;
}

.done{
	width:200px;
	position:relative;
	left: 0;
	right: 0;
	top:20px;
	margin:auto;
}
.contain h1{
	font-family: 'Julius Sans One', sans-serif;
    color: #a9427f;
    font-size: 50px;
}

.congrats{
	position:relative;
	left:50%;
top:50%;
	max-width:800px;	transform:translate(-50%,-50%);
	width:80%;
	min-height:100%;
	max-height:900px;
	border:2px solid white;
	border-radius:5px;
	    box-shadow: 12px 15px 20px 0 rgba(46,61,73,.3);
    background-image: #dba9c9 !important;
	background: #dba9c9 !important;
	text-align:center;
	font-size:3em;
	color: #000;
}

.text{
	position:relative;
	font-weight:normal;
	left:0;
	right:0;
	margin:auto;
	max-width:800px;

	font-family: 'Lato', sans-serif;
	font-size:0.6em;

}


.circ{
    opacity: 0;
    stroke-dasharray: 130;
    stroke-dashoffset: 130;
    -webkit-transition: all 1s;
    -moz-transition: all 1s;
    -ms-transition: all 1s;
    -o-transition: all 1s;
    transition: all 1s;
}
.tick{
    stroke-dasharray: 50;
    stroke-dashoffset: 50;
    -webkit-transition: stroke-dashoffset 1s 0.5s ease-out;
    -moz-transition: stroke-dashoffset 1s 0.5s ease-out;
    -ms-transition: stroke-dashoffset 1s 0.5s ease-out;
    -o-transition: stroke-dashoffset 1s 0.5s ease-out;
    transition: stroke-dashoffset 1s 0.5s ease-out;
}
.drawn svg .path{
    opacity: 1;
    stroke-dashoffset: 0;
}

.regards{
	font-size:.7em;
}


@media (max-width:600px){
	.congrats h1{
		font-size:1.2em;
	}
	
	.done{
		top:10px;
		width:80px;
		height:80px;
	}
	.text{
		font-size:0.5em;
	}
	.regards{
		font-size:0.6em;
	}
}

@media (max-width:500px){
	.congrats h1{
		font-size:1em;
	}
	
	.done{
		top:10px;
		width:70px;
		height:70px;
	}
	
}

@media (max-width:410px){
	.congrats h1{
		font-size:1em;
	}
	
	.congrats .hide{
		display:none;
	}
	
	.congrats{
		width:100%;
	}
	
	.done{
		top:10px;
		width:50px;
		height:50px;
	}
	.regards{
		font-size:0.55em;
	}
	
}
ol{
    padding: 10%;
    text-align: left;
    font-size: 35px;
}
ol li{
	padding:10px;
}
button{
    background: green;
    color: #fff;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 35px;
    font-weight: bold;
}
.download{
	padding: 10px 20px;
    margin-top: 50px;
    font-size: 40px;
    background: #efefef;
    border-radius: 10px;
    border: 2px solid;
    margin-bottom: 50px;
    bottom: 50px;
    text-decoration: none;
    font-weight: bold;
	 animation: glowing 1300ms infinite;
}
 @keyframes glowing {
        0% {
          background-color: #a9427f;
          box-shadow: 0 0 5px #a9427f;
        }
        50% {
          background-color: #dba9c9;
          box-shadow: 0 0 20px #dba9c9;
        }
        100% {
          background-color: #a9427f;
          box-shadow: 0 0 5px #a9427f;
        }
      }
.heading{
	padding: 30px 58px;
    background: #a9427f;
    border: 2px solid #000;
    color: #000;
    font-size: 40px;
	text-align: justify;
}
</style>
<div class="contain">
	<div class="congrats">
		
		<div class="done">
		<img style="width:200px" src="{{ uploaded_asset(get_setting('system_logo_black')) }}">
			</div>
		<div class="text">
		<h3 class="heading">Join Bazarnao using my invitation code and we'll both get rewards from Bazanao.
		</h3>
        <h1>Code : <span id="code">{{$code}}</span> &nbsp;&nbsp; <button id="code_copy" onclick="copy('code')">Copy</button> </h1>
			
			</div>
    <ol type="1">
            	
            	<li>Download Bazarnao APP</li>
            	<li>Register a new account on Bazarnao</li>
    <li>Enter <b>{{$user->name}}'s</b> referral code from profile</li>
            	<li>Enjoy rewards from Bazarnao </li>
            	
        	</ol>
    <a href="https://play.google.com/store/apps/details?id=com.bazarnao.app" class="download">Download Bazarnao App</a>
	</div>
</div>
<script>
function copy(element_id){
  var aux = document.createElement("div");
  aux.setAttribute("contentEditable", true);
  aux.innerHTML = document.getElementById(element_id).innerHTML;
  aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)"); 
  document.body.appendChild(aux);
  aux.focus();
  document.execCommand("copy");
  document.body.removeChild(aux);
  document.getElementById('code_copy').innerHTML = 'Copied';
}
</script>