var d = document;
var b = document.getElementsByTagName('body');


var canvas;
var gl;

var mvMatrixStack = [];
var horizAspect = 600.0/800.0;
var shaderProgram;
var rotation = 180;
var moveX = 0;
var moveY = 0;
var texture;
var texture2;
var tempDate = new Date();
var currentPressedKeys = Array();
var mouse1 = 0;
var sources = ['b_verh.obj'];
var models = {};
var textures = [];
var pos = [{x:15, y:15},{x:-15,y:-15},{x:-15,y:15},{x:15,y:-15}];

var floor = {
	vertices: [
            -1000.0, -1000.0, 1.0,
             1000.0, -1000.0, 1.0,
             1000.0, 1000.0, 1.0,
            -1000.0, 1000.0, 1.0
	],
	tCoords: [
            0.0, 0.0,
            10.0, 0.0,
            10.0, 10.0,
            0.0, 10.0
	]
}

var bullet = {
	vertices: [
            -10.0, -10.0, 0.0,
             10.0, -10.0, 0.0,
             10.0, 10.0, 0.0,
            -10.0, 10.0, 0.0
	],
	tCoords: [
            0.0, 0.0,
            1.0, 0.0,
            1.0, 1.0,
            0.0, 1.0
	]	
}

function increaseAngle() {
	if (currentPressedKeys[39]) {
		moveX += 0.1;
	}
	else if (currentPressedKeys[37]) {
		moveX -= 0.1;
	}
	if (currentPressedKeys[38]) {
		moveY += 0.1;
	}
	else if (currentPressedKeys[40]) {
		moveY -= 0.1;
	}
}

function init(){
	canvas = d.getElementById('canvas');
	initGL(canvas);

	initBuffers();
	initShaders();
	initTexture();

	initModels();
	p = new player('untitled.obj', 0.0, 0.0, texture2);
	console.log(p);

	gl.clearColor(0.3, 0.3, 0.6, 1.0);
	gl.enable(gl.DEPTH_TEST);

	document.onkeydown = handleKeyDown;
	document.onkeyup = handleKeyUp;
	document.onmousedown = handleMouseDown;
	document.onmouseup = handleMouseUp;

	loadIdentity();
	frame();
}

function initTexture() {
	var timage = new Image();
	texture = gl.createTexture();
	texture.image = timage;

	texture.image.onload = function() {
		handleLoadedTexture(texture);
	}
	texture.image.src = "grass.gif";

	var t2image = new Image();
	texture2 = gl.createTexture();
	texture2.image = t2image;

	texture2.image.onload = function() {
		handleLoadedTexture(texture2);
	}

	texture2.image.src = "t2.gif";
}

function initModels() {
	var i =0;
	for (s in sources) {
		models[sources[s]] = new model(sources[s], pos[i].x, pos[i].y, texture2)
		i++;
		console.log(sources[s]);
	}
}

function handleLoadedTexture(texture) {
	gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
	gl.bindTexture(gl.TEXTURE_2D, texture);
	gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, texture.image);
	gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
	gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR_MIPMAP_NEAREST);
	gl.generateMipmap(gl.TEXTURE_2D);

	gl.bindTexture(gl.TEXTURE_2D, null);
}

function initBuffers() {

	floorVerticesBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, floorVerticesBuffer);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(floor.vertices), gl.STATIC_DRAW);

	floorTexturesBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, floorTexturesBuffer);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(floor.tCoords), gl.STATIC_DRAW);

	floorIndexBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, floorIndexBuffer);
	gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array([0,1,2,0,2,3]), gl.STATIC_DRAW);

	bulletVerticesBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, bulletVerticesBuffer);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(bullet.vertices), gl.STATIC_DRAW);

	bulletTexturesBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, bulletTexturesBuffer);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(bullet.tCoords), gl.STATIC_DRAW);

	bulletIndexBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, bulletIndexBuffer);
	gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array([0,1,2,0,2,3]), gl.STATIC_DRAW);
}


function myBindBuffer(buffer, attrib, size) {
	gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
	gl.vertexAttribPointer(attrib, size, gl.FLOAT, false, 0, 0);
}
function drawScene() {
	perspectiveMatrix = makePerspective(45, 800.0/600.0, 0.1, 100.0);

	loadIdentity();

	mvTranslate([0,0,-57])
	mvTranslate([-moveX,-moveY,0])
	mvRotate(rotation, [0,1,0])

	gl.activeTexture(gl.TEXTURE0);
	gl.bindTexture(gl.TEXTURE_2D, texture);
	gl.uniform1i(shaderProgram.samplerUniform, 0);

	myBindBuffer(floorVerticesBuffer, vertexPositionAttribute, 3);
	myBindBuffer(floorTexturesBuffer, textureCoordAttribute, 2);
	gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, floorIndexBuffer);

	setMatrixUniforms();

	gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);

	if (mouse1 == 1) {;
		mvPushMatrix()
		mvTranslate([-moveX,moveY,0])

		gl.activeTexture(gl.TEXTURE0);
		gl.bindTexture(gl.TEXTURE_2D, texture2);
		gl.uniform1i(shaderProgram.samplerUniform, 0);

		myBindBuffer(bulletVerticesBuffer, vertexPositionAttribute, 3);
		myBindBuffer(bulletTexturesBuffer, textureCoordAttribute, 2);
		gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, bulletIndexBuffer);

		setMatrixUniforms();

		gl.drawElements(gl.TRIANGLES, 6, gl.UNSIGNED_SHORT, 0);
		mvPopMatrix();
	}
}
function initGL(canvas){

	try {
		gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
	} 
	catch(e) {}

	if (!gl) {
		alert('no gl');
	} else {
		return 'gl';
	}
}

function frame() {
	mozRequestAnimationFrame(frame);
	gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
	increaseAngle();

	drawScene();
	for (m in models) {
		models[m].draw();
	}
	p.draw();
}

function initShaders() {
	var fragmentShader = getShader(gl, "shader-fs");
	var vertexShader = getShader(gl, "shader-vs");
	
	// Create the shader program
	
	shaderProgram = gl.createProgram();
	gl.attachShader(shaderProgram, vertexShader);
	gl.attachShader(shaderProgram, fragmentShader);
	gl.linkProgram(shaderProgram);
	
	// If creating the shader program failed, alert
	
	if (!gl.getProgramParameter(shaderProgram, gl.LINK_STATUS)) {
		alert("Unable to initialize the shader program.");
	}
	
	gl.useProgram(shaderProgram);
	
	vertexPositionAttribute = gl.getAttribLocation(shaderProgram, "aVertexPosition");
	gl.enableVertexAttribArray(vertexPositionAttribute);

	textureCoordAttribute = gl.getAttribLocation(shaderProgram, "aTextureCoord");
	gl.enableVertexAttribArray(textureCoordAttribute);

	shaderProgram.samplerUniform = gl.getUniformLocation(shaderProgram, "uSampler");

}

function getShader(gl, id) {
	var shaderScript, theSource, currentChild, shader;
  
	shaderScript = document.getElementById(id);
  
	if (!shaderScript) {
		return null;
	}
  
	theSource = "";
	currentChild = shaderScript.firstChild;
  
	while(currentChild) {
		if (currentChild.nodeType == currentChild.TEXT_NODE) {
			theSource += currentChild.textContent;
		}
		currentChild = currentChild.nextSibling;
	} 
	if (shaderScript.type == "x-shader/x-fragment") {
		shader = gl.createShader(gl.FRAGMENT_SHADER);
	} else if (shaderScript.type == "x-shader/x-vertex") {
		shader = gl.createShader(gl.VERTEX_SHADER);
	} else {
	// Unknown shader type
		return null;
	}
	gl.shaderSource(shader, theSource);
    
	// Compile the shader program
	gl.compileShader(shader);  
    
	// See if it compiled successfully
	if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {  
		alert("An error occurred compiling the shaders: " + gl.getShaderInfoLog(shader));  
		return null;  
	}
	return shader;
}


function setMatrixUniforms() {
	var pUniform = gl.getUniformLocation(shaderProgram, "uPMatrix");
	gl.uniformMatrix4fv(pUniform, false, new Float32Array(perspectiveMatrix.flatten()));

	var mvUniform = gl.getUniformLocation(shaderProgram, "uMVMatrix");
	gl.uniformMatrix4fv(mvUniform, false, new Float32Array(mvMatrix.flatten()));

	var normalMatrix = mvMatrix.inverse();
	normalMatrix = normalMatrix.transpose();
	var nUniform = gl.getUniformLocation(shaderProgram, "uNormalMatrix");
	gl.uniformMatrix4fv(nUniform, false, new Float32Array(normalMatrix.flatten()));
}

window.addEventListener('load', init, false);

function handleKeyUp(ev) {
	currentPressedKeys[ev.keyCode] = 0;
}

function handleKeyDown(ev) {
	currentPressedKeys[ev.keyCode] = 1;
}

function handleMouseDown(ev) {
	console.log(ev.keyCode);
	mouse1 = 1;
}

function handleMouseUp(ev) {
	console.log('buum');	
	mouse1 = 0;
}

//oooo000000000000000000000000000000oooooooooooooo

function loadIdentity() {
	mvMatrix = Matrix.I(4);
}
function multMatrix(m) {
	mvMatrix = mvMatrix.x(m);
}
function mvTranslate(v) {
multMatrix(Matrix.Translation($V([v[0], v[1], v[2]])).ensure4x4());
}
function setMatrixUniforms() {
var pUniform = gl.getUniformLocation(shaderProgram, "uPMatrix");
gl.uniformMatrix4fv(pUniform, false, new Float32Array(perspectiveMatrix.flatten()));
var mvUniform = gl.getUniformLocation(shaderProgram, "uMVMatrix");
gl.uniformMatrix4fv(mvUniform, false, new Float32Array(mvMatrix.flatten()));
var normalMatrix = mvMatrix.inverse();
normalMatrix = normalMatrix.transpose();
var nUniform = gl.getUniformLocation(shaderProgram, "uNormalMatrix");
gl.uniformMatrix4fv(nUniform, false, new Float32Array(normalMatrix.flatten()));
}
var mvMatrixStack = [];
function mvPushMatrix(m) {
if (m) {
mvMatrixStack.push(m.dup());
mvMatrix = m.dup();
} else {
mvMatrixStack.push(mvMatrix.dup());
}
}
function mvPopMatrix() {
if (!mvMatrixStack.length) {
throw("Can't pop from an empty matrix stack.");
}
mvMatrix = mvMatrixStack.pop();
return mvMatrix;
}
function mvRotate(angle, v) {
var inRadians = angle * Math.PI / 180.0;
var m = Matrix.Rotation(inRadians, $V([v[0], v[1], v[2]])).ensure4x4();
multMatrix(m);
}

