/*
    This Application Create and Develop by Muammar
    Developed at 2020
*/

/* === Form Input Start === */

/*
 * Input Text For
 * @return String HTML
 */

function inputTextForm( label, name, value, className, att, labelAtt){
   if( className == undefined || className == '' ){
      className = 'col-lg-3';
   }
   return `<div class="${className}">
            <div class="form-group form-group-input row">
               <label class="col-sm-12 col-form-label">${label} ${labelAtt}</label>
               <div class="col-sm-12">
                  <input type="text" ${att} name="${name}" placeholder="${label}" class="form-control form-control-sm" id="${name}" value="${value}">
               </div>
            </div>
         </div>`;
}


/*
 * Input password Form
 * @return String HTML
 */
function inputPasswordForm(label, id_name, placeholder, att) {
	return `<div class="form-group">
                <label class="col-form-label col-form-label-sm" >${label}</label>
                <input class="form-control form-control-sm" type="password" name="${id_name}" id="${id_name}" placeholder="${placeholder}" style="font-size: 12px;" ${att} >
            </div>`;
}

//
function currencyForm(label, name, value, att){
   if( att == undefined ){
      att = '';
   }
   return  `<div class="form-group form-group-input">
               <label class="col-sm-12 col-form-label">${label}</label>
               <input type="text" name="${name}" placeholder="${label}" id="${name}"  ${att} class="currency form-control form-control-sm" value="${value}" />
            </div>`;
}

/*
 * Input Date Form
 * @return String HTML
 */
function inputDateForm(label, id_name, placeholder, att) {
	return `<div class="form-group">
                <label class="col-form-label col-form-label-sm" >${label}</label>
                <input class="form-control form-control-sm" type="date" name="${id_name}" id="${id_name}" placeholder="${placeholder}" style="font-size: 12px;" ${att}>
            </div>`;
}

/*
 * Textarea Form
 * @return String HTML
 */
function textareaForm(label, id_name, placeholder, att, val = "") {
	return `<div class="form-group">
                <label class="col-form-label col-form-label-sm" for="inputSmall">${label}</label>
                <textarea ${att} class="form-control" id="${id_name}" name="${id_name}" rows="3" style="resize:none;"  placeholder="${placeholder}" required>${val}</textarea>
            </div>`;
}

/*
 * Select Form
 * @return String HTML
 */
function selectForm(label, id_name, JSONdata, att, id, classFormGroup) {
	data = JSON.parse(JSONdata);
	var list = "";
	for (x in data) {
		if (x == id) {
			list += `<option value="${x}" selected>${data[x]}</option>`;
		} else {
			list += `<option value="${x}">${data[x]}</option>`;
		}
	}
	return `<div class="form-group ${classFormGroup}">
                <label class="col-form-label col-form-label-sm" >${label}</label>
                 <select class="js-example-basic-single" name="${id_name}" id="${id_name}" style="font-size:12px;" ${att}>
                    ${list}
                </select>
            </div>
            <script>
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
            </script>`;
}

function selectFormWithPlus(label, id_name, JSONdata, att, id, trigger_func) {
	data = JSON.parse(JSONdata);
	var list = "";
	for (x in data) {
		if (x == id) {
			list += `<option value="${x}" selected>${data[x]}</option>`;
		} else {
			list += `<option value="${x}">${data[x]}</option>`;
		}
	}
	return `<div class="form-group">
                <label class="col-form-label col-form-label-sm" >${label}</label>
                <div class="row">
					<div class="col-10">
		                <select class="js-example-basic-single" name="${id_name}" id="${id_name}" style="font-size:12px;" ${att}>
		                    ${list}
		                </select>
		            </div>
		            <div class="col-2">
						<a onclick="${trigger_func}()" class="btn btn-info" style="float:right;">
							<i class="fas fa-plus"></i>
						</a>
					</div>
				</div>
	        </div>
            <script>
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
            </script>`;
}

function selectFormModif(label, id_name, JSONdata, att, id) {
	data = JSON.parse(JSONdata);
	var list = "";
	for (x in data) {
		if (x == id) {
			list += `<option value="${data[x]}" selected>${data[x]}</option>`;
		} else {
			list += `<option value="${data[x]}">${data[x]}</option>`;
		}
	}
	return `<div class="form-group">
                <label class="col-form-label col-form-label-sm" >${label}</label>
                 <select class="js-example-basic-single" name="${id_name}" id="${id_name}" style="font-size:12px;" ${att}>
                    ${list}
                </select>
            </div>
            <script>
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
            </script>`;
}

/*
 * Hidden Form
 * @return String HTML
 */

// formListCheckbox
 function multipleCheckbox(label, name, JSONdata, width , listValue){
    var data = JSON.parse(JSONdata);
    var list = [];
    if( listValue != undefined ){
          list = JSON.parse(listValue);
    }
    var checked = '';
    var html = `<fieldset class="form-group row">
                   <label class="col-sm-12 col-form-label">${label}</label>`;
       for( x in data ) {
          checked = '';
          if( listValue != undefined ){
             if(list[x] != undefined ){
                checked = ' checked ';
             }
          }
          html += `<div class="col-lg-${width}">
                      <div class="form-check">
                         <label class="form-check-label">
                            <input class="form-check-input" name="${name}[${x}]" type="checkbox"
                               value="${x}" ${checked}>
                            ${data[x]}
                         </label>
                      </div>
                   </div>`;
       }
       html += `</fieldset>`;
    return html;
 }

// form add muthawif
function formAddMuthawifPaket(label, JSONdata, value){
   var listMuthawif = JSON.parse(JSONdata);
   var html = `<div class="col-12 col-lg-12 px-3" >
                   <input type="hidden" id="jsonMuthawif" value='${JSONdata}'>
                   <div class="form-group form-group-input row">
                      <label for="exampleSelect1" class="col-sm-12 col-form-label">${label}</label>
                      <div class="col-sm-12 px-0" id="listMuthawif">`;
         if( value != undefined )
         {
            var data = JSON.parse(value);
            if( value.length > 0 )
            {
               for( y in value )
               {
                  html += `<div class="row" >
                              <div class="col-sm-10 py-1">
                                 <select class="form-control form-control-sm" name="muthawif[]">
                                    <option value="0">Pilih Muthawif</option>`;
                  for( x in listMuthawif )
                  {
                        html += `<option value="${x}" ${ value[y] ==  x ? ' selected ' : ''} >${listMuthawif[x]}</option>`;
                  }
                  html +=       `</select>
                              </div>
                              <div class="col-sm-2 py-1 px-0 pr-2 text-right">
                                 <button class="btn btn-default btn-action" title="Delete" onclick="delete()">
                                     <i class="fas fa-times" style="font-size: 11px;"></i>
                                 </button>
                              </div>
                           </div>`;
               }
            } else {
               html += `<div class="row" >
                           <div class="col-sm-10 py-1">
                              <select class="form-control form-control-sm" name="muthawif[]">
                                 <option value="0">Pilih Muthawif</option>`;
               for( x in listMuthawif )
               {
                     html += `<option value="${x}">${listMuthawif[x]}</option>`;
               }
               html +=       `</select>
                           </div>
                           <div class="col-sm-2 py-1 px-0 pr-2 text-right">
                              <button type="button" class="btn btn-default btn-action" title="Delete Muthawif" onclick="deleteMuthawif(this)">
                                  <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>`;
            }
         }else{
            html += `<div class="row" >
                        <div class="col-sm-10 py-1">
                           <select class="form-control form-control-sm" name="muthawif[]">
                              <option value="0">Pilih Muthawif</option>`;
            for( x in listMuthawif )
            {
               html += `<option value="${x}">${listMuthawif[x]}</option>`;
            }
            html +=        `</select>
                        </div>
                        <div class="col-sm-2 py-1 px-0 pr-2 text-right">
                           <button type="button" class="btn btn-default btn-action" title="Delete Muthawif" onclick="deleteMuthawif(this)">
                              <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>
                         </div>
                     </div>`;
         }
       html +=   `</div>
                  <div class="col-sm-12 px-0 pt-2" >
                      <button type="button" class="btn btn-default btn-action" title="Tambah Muthawif" onclick="tambahMuthawif()" style="width:100%;">
                         <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Muthawif
                      </button>
                   </div>
                </div>
             </div>`;
    return html;
 }


function hiddenForm(id_name, val) {
	return `<input type="hidden" name="${id_name}" id="${id_name}" value="${val}">`;
}

function checkBox(label, Name, att, classDiv) {
	return `<div class="form-check ${classDiv}">
					<label class="form-check-label">
						<input class="form-check-input" type="checkbox" name="${Name}" ${att}>${label}
					</label>
				</div>`;
}

/*
 * PositionForm
 * @return String HTML
 */
function positionForm(iconName, menuName) {
	return `<div class="btn"><i class="nav-icon ${iconName}" style="display: inline-block;font-size: 19px;color: #848484;"></i>
    <h1 class="m-0 text-dark stack" style="font-size: 15px;color: #cabfbf!important;display: inline-block;">${menuName}</h1></div>`;
}

/*
 * BreadCumForm
 * @return String HTML
 */
function breadcumForm(JSONData) {
	var data = JSON.parse(JSONData);
	var leng = data.length;
	var breadFeed = "";
	for (x in data) {
		if (x == leng - 1) {
			breadFeed += `<li class="breadcrumb-item active">${data[x]}</li>`;
		} else {
			breadFeed += `<li class="breadcrumb-item">
                            <a >${data[x]}</a>
                          </li>`;
		}
	}
	return breadFeed;
}

/* ==== Form Input End === */

/*
 * Logout Function
 * @return null
 */
function logout() {
	ajax_x(
		baseUrl + "Main/logout",
		function(data) {
			if (data["error"] == false) {
				window.location.href = baseUrl;
			}
		},
		[]
	);
}

/*
 * Logout Function
 * @return null
 */
function alertRed(message) {
	$.alert({
		title: '<span style="color:red;font-weight:bold;">ERROR</span>',
		type: "red",
		content: message,
		typeAnimated: true
	});
}

function gen_chartLine(
	ylabel,
	xlabel,
	idCanvas,
	arrData,
	background,
	callback
) {
	let datas = JSON.parse(arrData);
	let arrXlabel = JSON.parse(xlabel);

	if (background == undefined || background == "") {
		realBackground = [
			"rgba(225,0,0,0.7)",
			"rgb(54,162,235, 0.7)",
			"rgba(41,133,35,0.7)",
			"rgba(220,220,120,0.7)",
			"rgba(167,105,0,0.7)"
		];
	} else {
		realBackground = background;
	}

	var ctx = document.getElementById(idCanvas).getContext("2d");

	let datasetLine = [];
	var s = 0;
	for (x in datas) {
		datasetLine.push({
			label: datas[x]["label_dataset"],
			fill: false,

			backgroundColor: realBackground[s],
			borderColor: realBackground[s], // The main line color
			borderCapStyle: "square",
			borderDash: [], // try [5, 15] for instance
			borderDashOffset: 0.0,
			borderJoinStyle: "miter",
			pointBorderColor: "black",
			pointBackgroundColor: "#20bfd6f2",
			pointBorderWidth: 1,
			pointHoverRadius: 8,
			pointHoverBackgroundColor: "yellow",
			pointHoverBorderColor: "brown",
			pointHoverBorderWidth: 2,
			pointRadius: 4,
			pointHitRadius: 10,
			// notice the gap in the data and the spanGaps: true
			data: datas[x]["dataset"],
			spanGaps: true
		});
		s++;
	}

	var data = {
		labels: arrXlabel,
		datasets: datasetLine
	};

	if (typeof callback === "function" && callback()) {
		var options = callback();
	} else {
		var options = {
			responsive: true,
			tooltips: {
				mode: "index",
				intersect: false
			},
			scales: {
				yAxes: [
					{
						ticks: {
							beginAtZero: true
						},
						scaleLabel: {
							display: true,
							labelString: ylabel,
							fontSize: 12
						}
					}
				]
			}
		};
	}

	var myChart = new Chart(ctx, {
		type: "line",
		data: data,
		options: options
	});
}

function get_chartDonuct(ylabel, xlabel, idCanvas, arrData, background) {
	let datas = JSON.parse(arrData);
	let arrXlabel = JSON.parse(xlabel);

	if (background == undefined || background == "") {
		realBackground = [
			"rgba(225,0,0,0.7)",
			"rgb(54,162,235, 0.7)",
			"rgba(41,133,35,0.7)",
			"blue",
			"yellow",
			"orange",
			"black",
			"rgba(255, 99, 132, 0.2)",
			"rgba(255, 206, 86, 0.2)",
			"rgba(75, 192, 192, 0.2)",
			"rgba(153, 102, 255, 0.2)",
			"rgba(255, 159, 64, 0.2)",
			"rgba(255, 99, 132, 1)",
			"rgba(54, 162, 235, 1)",
			"rgba(255, 206, 86, 1)",
			"rgba(75, 192, 192, 1)",
			"rgba(153, 102, 255, 1)",
			"rgba(255, 159, 64, 1)"
		];
	} else {
		realBackground = background;
	}

	var config = {
		type: "doughnut",
		data: {
			datasets: [
				{
					data: datas,
					backgroundColor: realBackground,
					label: "Dataset 1"
				}
			],
			labels: arrXlabel
		},
		options: {
			responsive: true,
			legend: {
				position: "top"
			},
			title: {
				display: true,
				text: ylabel
			},
			animation: {
				animateScale: true,
				animateRotate: true
			}
		}
	};

	var ctx = document.getElementById(idCanvas).getContext("2d");
	var myDoughnutChart = new Chart(ctx, config);
}

/**
 * Ajax Function
 * @return callback
 */
function ajax_x(urls, callback, datas) {

	var csrfName = localStorage.getItem("csrfName");
	var csrfHash = localStorage.getItem("csrfHash");

	var data = {};
	data[csrfName] = csrfHash;

	for (x in datas[0]) {
		data[x] = datas[0][x];
	}

	$.ajax({
		url: urls,
		type: "post",
		data: data,
		dataType: "json",
		beforeSend: function() {
			$("#loader").show();
		},
		success: function(data) {
			if(data['error'] == true ){
				$("#loader").hide();
			}
			localStorage.setItem('csrfHash', data[csrfName]);
			callback(data);
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}


/**
 * Ajax Function
 * @return callback
 */
function ajax_x_t2(urls, callback, datas) {

	var csrfName = localStorage.getItem("csrfName");
	var csrfHash = localStorage.getItem("csrfHash");

	var data = {};
	data[csrfName] = csrfHash;

	for (x in datas[0]) {
		data[x] = datas[0][x];
	}

	$.ajax({
		url: urls,
		type: "post",
		data: data,
		dataType: "json",
		// beforeSend: function() {
		// 	$("#loader").show();
		// },
		success: function(data) {
			if(data['error'] == true ){
				$("#loader").hide();
			}
			localStorage.setItem('csrfHash', data[csrfName]);
			callback(data);
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}

/**
 * Ajax submit Function
 * @return callback
 */

function ajax_submit_t2(is, callback) {

 	var csrfName = localStorage.getItem("csrfName");
 	var csrfHash = localStorage.getItem("csrfHash");

 	var action = $(is).attr("action");
 	var formData = new FormData($(is)[0]);
 	formData.append(csrfName, csrfHash);

 	$.ajax({
 		url: action,
 		type: "post",
 		data: formData,
 		mimeType: "multipart/form-data",
 		contentType: false,
 		cache: false,
 		processData: false,
 		dataType: "json",
 		beforeSend: function() {
 			$("#loader").show();
 		},
 		success: function(data) {
 			localStorage.setItem('csrfHash', data[csrfName]);
 			callback(data);
 		},
 		error: function(request, status, error) {
 			swal({ html: true, title: request.responseText, icon: "error" });
 		},
 		complete: function() {
 			$("#loader").hide();
 		}
 	});
}

function hide_currency(price){
   if(price.includes(".")){
      price = price.replace(/\./g, "");
   }
   console.log("setelah replace =====");
   console.log(price);
   console.log("setelah replace =====");
   var priceNum = Number(price.replace(/[^0-9\.-]+/g,""));
   return priceNum;
}

function numberFormat(x) {
    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
}

function ajax_submit_t1(is, callback) {

	var csrfName = localStorage.getItem("csrfName");
	var csrfHash = localStorage.getItem("csrfHash");

	var action = $(is).attr("action");
	var formData = new FormData($(is)[0]);
	formData.append(csrfName, csrfHash);

	$.ajax({
		url: action,
		type: "post",
		data: formData,
		mimeType: "multipart/form-data",
		contentType: false,
		cache: false,
		processData: false,
		dataType: "json",
		beforeSend: function() {
			$("#loader").show();
		},
		success: function(data) {
			localStorage.setItem('csrfHash', data[csrfName]);
			if (data["error"] == true) {
				$.alert({
					title: '<span style="color:red;font-weight:bold;">ERROR</span>',
					type: "red",
					content: data["error_msg"],
					typeAnimated: true
				});
			} else {
				callback(data);
			}
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}

function ajax_submit(e, is, callback) {
	e.preventDefault();

	var csrfName = localStorage.getItem("csrfName");
	var csrfHash = localStorage.getItem("csrfHash");

	var action = $(is).attr("action");
	var formData = new FormData($(is)[0]);
	formData.append(csrfName, csrfHash);

	$.ajax({
		url: action,
		type: "post",
		data: formData,
		mimeType: "multipart/form-data",
		contentType: false,
		cache: false,
		processData: false,
		dataType: "json",
		beforeSend: function() {
			$("#loader").show();
		},
		success: function(data) {
			localStorage.setItem('csrfHash', data[csrfName]);
			if (data["error"] == true) {
				$.alert({
					title: '<span style="color:red;font-weight:bold;">ERROR</span>',
					type: "red",
					content: data["error_msg"],
					typeAnimated: true
				});
			} else {
				callback(data);
			}
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}



function ajax_submit_base64(e, is, callback) {
	e.preventDefault();

	var csrfName = localStorage.getItem("csrfName");
	var csrfHash = localStorage.getItem("csrfHash");

	var action = $(is).attr("action");
	var formData = new FormData($(is)[0]);
	formData.append(csrfName, csrfHash);
   if ( $( "#base64image" ).length ) {
      var file =  document.getElementById("base64image").src;
      formData.append("base64image", file);
   }

	$.ajax({
		url: action,
		type: "post",
		data: formData,
		mimeType: "multipart/form-data",
		contentType: false,
		cache: false,
		processData: false,
		dataType: "json",
		beforeSend: function() {
			$("#loader").show();
		},
		success: function(data) {
			localStorage.setItem('csrfHash', data[csrfName]);
			if (data["error"] == true) {
				$.alert({
					title: '<span style="color:red;font-weight:bold;">ERROR</span>',
					type: "red",
					content: data["error_msg"],
					typeAnimated: true
				});
			} else {
				callback(data);
			}
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}



/**
 * login submit model 1
 * @return redirecting
 */
function login_submit(e, is, callback) {
	e.preventDefault();
	var action = $(is).attr("action");
	var formData = new FormData($(is)[0]);
	$.ajax({
		url: action,
		type: "post",
		data: formData,
		mimeType: "multipart/form-data",
		contentType: false,
		cache: false,
		processData: false,
		dataType: "json",
		beforeSend: function() {
			$("#loader").show();
		},
		success: function(data) {
			// localStorage.setItem(csrfName, data[csrfName]);
			if (data["error"] == true) {
				$.alert({
					title: '<span style="color:red;font-weight:bold;">ERROR</span>',
					type: "red",
					content: data["error_msg"],
					typeAnimated: true
				});
			} else {
				callback(data);
			}
		},
		error: function(request, status, error) {
			swal({ html: true, title: request.responseText, icon: "error" });
		},
		complete: function() {
			$("#loader").hide();
		}
	});
}



// navigation button
// function navigationButton(label, label2, fn, icon, classes){
//    Webcam.reset();
//    return  `<button type="button" class="btn btn-default btn-sm navbtn  ${classes}"
//                onclick="navBtn(this, '${fn}', '${label2}')">
//                <i class="${icon}" style="font-size: 11px;"></i> <span class="d-none d-sm-none d-md-none d-lg-inline-block d-md-none">${label}</span>
//             </button>`;
// }
 // param, , label2,  fn,
// onclick="navBtnParam(this, '${fn}', '${param}', '${label2}')"
// navButtonParam
// function navigationButton(label, att, icon, classes){
//    // Webcam.reset();
//    return  `<button type="button" class="btn btn-default btn-sm navbtn  ${classes}" ${att}>
//                <i class="${icon}" style="font-size: 11px;"></i>
// 					<span class="d-none d-sm-none d-md-none d-lg-inline-block d-md-none">${label}</span>
//             </button>`;
// }



//window.location.href = data['url'];
