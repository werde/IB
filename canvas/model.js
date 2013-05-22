model = function(source, posX, posY, texture) {
	var request = new XMLHttpRequest();
	this.source = source;
	this.texture = texture;
	this.posX = posX;
	this.posY = posY;

	this.buffers = {
		vBuffer: null,
		tCoordsBuffer: null,
		vNormalsBuffer: null,
		indexArrayBuffer: null
	};

	var obj = this;
	request.onreadystatechange = function() {
		if (request.readyState == "4") {
			if (/.obj/i.test(source)) {
				console.log('obj');
				[obj.vertices, obj.tCoords, obj.vNormals, obj.indexArray] = doLoadObj(this.responseText);
			} else if(/.json/i.test(source)) {
				[obj.vertices, obj.tCoords, obj.vNormals, obj.indexArray] = doLoadObjJSON(JSON.parse(request.responseText));
				console.log('json');
			}

			obj.loaded = 1;

			obj.buffers.bvBuffer = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, obj.buffers.bvBuffer);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(obj.vertices), gl.STATIC_DRAW);

			obj.buffers.btCoordsBuffer = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, obj.buffers.btCoordsBuffer);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(obj.tCoords), gl.STATIC_DRAW);

			obj.buffers.bindexArrayBuffer = gl.createBuffer();
			gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, obj.buffers.bindexArrayBuffer);
			gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(obj.indexArray), gl.STATIC_DRAW);
		}
	}
	request.open("GET", source);
	request.send();
}

model.prototype.draw = function() {
	if (this.loaded != 1) {
		console.log('loading ' + this.source);
		return 0;
	}

	mvPushMatrix();
	mvTranslate([this.posX,this.posY,0])

	gl.activeTexture(gl.TEXTURE0);
	gl.bindTexture(gl.TEXTURE_2D, this.texture);
	gl.uniform1i(shaderProgram.samplerUniform, 0);

	gl.bindBuffer(gl.ARRAY_BUFFER, this.buffers.bvBuffer);
	gl.vertexAttribPointer(vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);

	gl.bindBuffer(gl.ARRAY_BUFFER, this.buffers.btCoordsBuffer);
	gl.vertexAttribPointer(textureCoordAttribute, 2, gl.FLOAT, false, 0, 0);

	gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.buffers.bindexArrayBuffer);

	setMatrixUniforms();
	gl.drawElements(gl.TRIANGLES, this.indexArray.length, gl.UNSIGNED_SHORT, 0);
	mvPopMatrix()
	return 1;
}

function player(source, posX, posY, texture) {
	 player.superclass.constructor.apply(this, [source, posX, posY, texture]);
};
function extend(Child, Parent) {
	    var F = function() { }
	    F.prototype = Parent.prototype;
	    Child.prototype = new F();
	    Child.prototype.constructor = Child;
	    Child.superclass = Parent.prototype;
}
extend(player, model);

player.prototype.draw = function() {
	if (this.loaded != 1) {
//		console.log(this.source);
		return 0;
	}

	mvPushMatrix();
	mvTranslate([-moveX,moveY,0])
	mvRotate(90, [1, 0 ,0])
	gl.activeTexture(gl.TEXTURE0);
	gl.bindTexture(gl.TEXTURE_2D, this.texture);
	gl.uniform1i(shaderProgram.samplerUniform, 0);

	gl.bindBuffer(gl.ARRAY_BUFFER, this.buffers.bvBuffer);
	gl.vertexAttribPointer(vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);

	gl.bindBuffer(gl.ARRAY_BUFFER, this.buffers.btCoordsBuffer);
	gl.vertexAttribPointer(textureCoordAttribute, 2, gl.FLOAT, false, 0, 0);

	gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.buffers.bindexArrayBuffer);

	setMatrixUniforms();
	gl.drawElements(gl.TRIANGLES, this.indexArray.length, gl.UNSIGNED_SHORT, 0);
	mvPopMatrix()
	return 1;
}








function doLoadObj(text) {
	vertexArray = [ ];
	normalArray = [ ];
	textureArray = [ ];
	indexArray = [ ];

	var vertex = [ ];
	var normal = [ ];
	var texture = [ ];
	var facemap = { };
	var index = 0;

	var lines = text.split("\n");
	for (var lineIndex in lines) {
		var line = lines[lineIndex].replace(/[ \t]+/g, " ").replace(/\s\s*$/, "");

		// ignore comments
		if (line[0] == "#")
			continue;

		var array = line.split(" ");
		if (array[0] == "v") {
			// vertex
			vertex.push(parseFloat(array[1]));
			vertex.push(parseFloat(array[2]));
			vertex.push(parseFloat(array[3]));
		}
		else if (array[0] == "vt") {
			// normal
			texture.push(parseFloat(array[1]));
			texture.push(parseFloat(array[2]));
		}
		else if (array[0] == "vn") {
			// normal
			normal.push(parseFloat(array[1]));
			normal.push(parseFloat(array[2]));
			normal.push(parseFloat(array[3]));
		}
		else if (array[0] == "f") {
		// face
			if (array.length != 4) {
				//obj.ctx.console.log("*** Error: face '"+line+"' not handled");
				continue;
			}

			for (var i = 1; i < 4; ++i) {
				if (!(array[i] in facemap)) {
				// add a new entry to the map and arrays
					var f = array[i].split("/");
					var vtx, nor, tex;

					if (f.length == 1) {
						vtx = parseInt(f[0]) - 1;
						nor = vtx;
						tex = vtx;
					}
					else if (f.length = 3) {
						vtx = parseInt(f[0]) - 1;
						tex = parseInt(f[1]) - 1;
						nor = parseInt(f[2]) - 1;
					}
					else {
						//obj.ctx.console.log("*** Error: did not understand face '"+array[i]+"'");
						return null;
					}

					// do the vertices
					var x = 0;
					var y = 0;
					var z = 0;
					if (vtx * 3 + 2 < vertex.length) {
						x = vertex[vtx*3];
						y = vertex[vtx*3+1];
						z = vertex[vtx*3+2];
					}
					vertexArray.push(x);
					vertexArray.push(y);
					vertexArray.push(z);

					// do the textures
					x = 0;
					y = 0;
					if (tex * 2 + 1 < texture.length) {
						x = texture[tex*2];
						y = texture[tex*2+1];
					}
					textureArray.push(x);
					textureArray.push(y);

					// do the normals
					x = 0;
					y = 0;
					z = 1;
					if (nor * 3 + 2 < normal.length) {
						x = normal[nor*3];
						y = normal[nor*3+1];
						z = normal[nor*3+2];
					}
					normalArray.push(x);
					normalArray.push(y);
					normalArray.push(z);

					facemap[array[i]] = index++;
				}

				indexArray.push(facemap[array[i]]);
			}
		}
	}

	result = {};
	result["vertexPositions"] = vertexArray;
	result["vertexNormals"] = normalArray;
	result["vertexTextureCoords"] = textureArray;
	result["indices"] = indexArray;
	return [vertexArray, textureArray, normalArray, indexArray];
}

function doLoadObjJSON(o) {
	return [o.vertexPositions,  o.vertexTextureCoords, o.vertexNormals, o.indices]
}

function extend(Child, Parent) {
	    var F = function() { }
	    F.prototype = Parent.prototype;
	    Child.prototype = new F();
	    Child.prototype.constructor = Child;
	    Child.superclass = Parent.prototype;
}