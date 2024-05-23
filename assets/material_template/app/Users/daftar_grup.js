function daftar_grup_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarGrup">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_grup()">
                        <i class="fas fa-plus"></i> Tambah Grup Pengguna
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" id="searchGroup" name="searchGroup" placeholder="Nama Grup" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_grup(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:35%;">Nama Grup</th>
                              <th style="width:30%;">Group Akses</th>
                              <th style="width:25%;">Last Update</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_grup">
                           <tr>
                              <td colspan="4">Daftar grup tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_grup"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_grup_getData(){
   get_daftar_grup(20);
}

function get_daftar_grup(perpage){
   get_data( perpage,
             { url : 'Daftar_grup/daftar_grups',
               pagination_id: 'pagination_daftar_grup',
               bodyTable_id: 'bodyTable_daftar_grup',
               fn: 'ListDaftarGrup',
               warning_text: '<td colspan="4">Daftar grup tidak ditemukan</td>',
               param : { search : $('#searchGroup').val() } } );
}

function ListDaftarGrup(JSONData){
   var json = JSON.parse(JSONData);
   var modul_akses = `<ul class="text-left">`;
   var submodul = [];
   for( x in json.group_access ) {
      if( Object.keys(json.group_access[x]).length > 0   ) {
         modul_akses += `<li>
                           <label class="mb-0">${x}</label>
                           <ul class="mb-1">`;
            var submodul = json.group_access[x];
            for( y in submodul ){
               modul_akses += `<li>${submodul[y]}</li>`;
            }
            modul_akses += `</ul>
                        </li>`;
      }else{
         modul_akses += `<li><label>${x}</label></li>`;
      }
   }
   modul_akses += `</ul>`;

   var html =  `<tr>
                  <td>${json.nama_group}</td>
                  <td>${modul_akses}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Grup"
                        onclick="edit_grup('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Grup"
                        onclick="delete_grup('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function edit_grup(id){
   ajax_x(
      baseUrl + "Daftar_grup/get_info_grup_edit", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Grup',
            theme: 'material',
            content: formaddupdate_grup(JSON.stringify(e['data']), JSON.stringify(e['value'])),
            closeIcon: false,
            buttons: {
               cancel:function () {
                    return true;
               },
               simpan: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' :'green'
                        });
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_grup(20);
                        }
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

// delete grup
function delete_grup(id){
   $.confirm({
      columnClass: 'col-4',
      title: 'Peringatan',
      theme: 'material',
      content: 'Jika anda menghapus grup, maka semua user yang berada dibawah grup ini akan ikut juga terhapus. Apakah anda ingin melanjutkan proses ini?.',
      // closeIcon: false,
      buttons: {
         cancel:function () {
              return true;
         },
         ya: {
            text: 'Ya',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                  baseUrl + "Daftar_grup/delete_grup", function(e) {
                     if(e['error'] == false){
                        get_daftar_grup(20);
                     }
                     e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                  },[{id:id}]
               );
            }
         }
      }
   });
}

function add_grup(){
   ajax_x(
      baseUrl + "Daftar_grup/get_info_grup", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Tambah Grup Baru',
            theme: 'material',
            content: formaddupdate_grup(JSON.stringify(e['data'])),
            closeIcon: false,
            buttons: {
               cancel:function () {
                    return true;
               },
               simpan: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' :'green'
                        });
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_grup(20);
                        }
                     });
                  }
               }
            }
         });
      },[]
   );
}

let checkValue = (value, arr) => {
   var status = false;
   for( var i=0; i<arr.length; i++ ) {
      var name = arr[i];
      if( name == value ) {
        status = true; break;
      }
   }
   return status;
}

function checkmenu(id) {
	$(".submenu_" + id)
		.children()
		.children()
		.prop("checked", true);
	$("#menu_" + id).removeAttr("onclick");
	$("#menu_" + id).attr("onclick", "discheckmenu(" + id + ")");
}

function discheckmenu(id) {
	$(".submenu_" + id)
		.children()
		.children()
		.prop("checked", false);
	$("#menu_" + id).removeAttr("onclick");
	$("#menu_" + id).attr("onclick", "checkmenu(" + id + ")");
}

function subcheckmenu(id) {
	$("#menu_" + id).prop("checked", true);
	$("#menu_" + id).removeAttr("onclick");
	$("#menu_" + id).attr("onclick", "discheckmenu(" + id + ")");
}

function formaddupdate_grup(JSONData, JSONValue){
   var data = JSON.parse(JSONData);
   var id_grup = '';
   var nama_grup = '';
   var modulValue = [];
   var submodulValue = [];

   if ( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id_grup = `<input type="hidden" name="id" value="${value.group_id}">`;
      nama_grup = value.nama_group;
      modulValue = value.modul;
      submodulValue = value.submodul;

      console.log('modulValue');
      console.log(modulValue);
      console.log('modulValue');
   }

   let listMenu = "";
   for ( x in data ) {
      let checkedmol = ` onClick="checkmenu(${data[x]['modul_id']})"`;
      if ( checkValue( data[x]['modul_id'], modulValue ) == true ) {
         checkedmol = ` onClick="discheckmenu(${data[x]['modul_id']})" checked `;
      }
      listMenu += checkBox( `<b>` +
         data[x]["modul_name"] + `</b>`,
         "menu[]",
         ` id="menu_${data[x]['modul_id']}" ${checkedmol} value="${data[x]['modul_id']}" `
      );

      if( data[x].submodul != undefined ) {
         var submodul = data[x]["submodul"];
         if ( Object.keys(submodul).length  > 0 ) {
           for (z in submodul) {
               let checkedsubmol = " ";
               if ( checkValue( x, modulValue ) == true ) {
                  checkedsubmol += " checked ";
               }
               listMenu += checkBox(
                  submodul[z]['submodules_name'],
                  "submenu[]",
                  ` value="${submodul[z]['submodul_id']}"  ${ checkedsubmol }  onClick="subcheckmenu(${x})"`,
                  `ml-3 submenu_${x}`
               );
           }
         }
      }

      listMenu += '<hr class="my-2" style="width:100%">';
   }

   var html = `<form action="${baseUrl }Daftar_grup/proses_addupdate_grup" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Grup Pengguna</label>
                                 <input type="text" name="nama_group" value="${nama_grup}" class="form-control form-control-sm" placeholder="Nama Grup Pengguna" />
                                 ${id_grup}
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Daftar Menu Yang Dapat Diakses</label>
                                 ${listMenu}
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}
