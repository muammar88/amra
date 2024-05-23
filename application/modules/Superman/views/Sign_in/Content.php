<body class="backgrounds">
   <style media="screen">
      body {
         color: #000;
         overflow-x: hidden;
         height: 100%;
         background-color: #B0BEC5;
         background-repeat: no-repeat
      }

      .card0 {
         box-shadow: 0px 4px 8px 0px #757575;
         border-radius: 0px
      }

      .card2 {
         margin: 0px 40px
      }

      .logo {
         width: 200px;
         height: 100px;
         margin-top: 20px;
         margin-left: 35px
      }

      .image {
         width: 360px;
         height: 280px
      }

      .border-line {
         border-right: 1px solid #EEEEEE
      }

      .facebook {
         background-color: #3b5998;
         color: #fff;
         font-size: 18px;
         padding-top: 5px;
         border-radius: 50%;
         width: 35px;
         height: 35px;
         cursor: pointer
      }

      .twitter {
         background-color: #1DA1F2;
         color: #fff;
         font-size: 18px;
         padding-top: 5px;
         border-radius: 50%;
         width: 35px;
         height: 35px;
         cursor: pointer
      }

      .linkedin {
         background-color: #2867B2;
         color: #fff;
         font-size: 18px;
         padding-top: 5px;
         border-radius: 50%;
         width: 35px;
         height: 35px;
         cursor: pointer
      }

      .line {
         height: 1px;
         width: 45%;
         background-color: #E0E0E0;
         margin-top: 10px
      }

      .text-sm {
         font-size: 14px !important
      }

      ::placeholder {
         color: #BDBDBD;
         opacity: 1;
         font-weight: 300
      }

      :-ms-input-placeholder {
         color: #BDBDBD;
         font-weight: 300
      }

      ::-ms-input-placeholder {
         color: #BDBDBD;
         font-weight: 300
      }

      input,
      textarea {
         padding: 10px 12px 10px 12px;
         border: 1px solid lightgrey;
         border-radius: 2px;
         margin-bottom: 5px;
         margin-top: 2px;
         width: 100%;
         box-sizing: border-box;
         color: #2C3E50;
         font-size: 14px;
         letter-spacing: 1px;
      }

      input:focus,
      textarea:focus {
         -moz-box-shadow: none !important;
         -webkit-box-shadow: none !important;
         box-shadow: none !important;
         border: 1px solid #304FFE;
         outline-width: 0;
      }

      button:focus {
         -moz-box-shadow: none !important;
         -webkit-box-shadow: none !important;
         box-shadow: none !important;
         outline-width: 0;
      }

      a {
         color: inherit;
         cursor: pointer;
      }

      .btn-superman {
          background-color: #c80000 !important;
          width: 150px;
          color: #fff;
          border-color: black;
          border-width: 3px;
          border-radius: 2px;
      }
      
      .btn-blue:hover {
         background-color: #132361;
         cursor: pointer;
         color: #fff;
      }

      .bg-blue {
         color: #fff;
         background-color: #033047 !important;
      }

      .bg-superman {
         color: #fff;
         background-color: #c80000 !important;
         border-right: 3px solid black;
         border-bottom: 3px solid black;
         border-left: 3px solid black;
         border-top: 3px solid black !important;
      }

      .card {
         box-shadow: none;
      }

      @media screen and (max-width: 991px) {
         .logo {
            margin-left: 0px
         }

         .image {
            width: 300px;
            height: 220px
         }

         .border-line {
            border-right: none
         }

         .card2 {
            border-top: 1px solid #EEEEEE !important;
            margin: 0px 15px
         }
      }
   </style>
   <div class="row" style="background-color: #1aa4b800;background-image: linear-gradient(141deg, #cacaca 0%, #fffffff2 75%) !important;min-height:100vh;">
      <div class="col-10 col-md-4 col-lg-3 px-0 py-1 mx-auto mt-4">
         <div class="row">
            <div class="col-12 py-4 mt-4">
               <div class="card card0 border-0 mt-5">
                  <div class="row d-flex">
                     <div class="col-12 ">
                        <div class="card2 card border-0 px-0 py-3">
                           <div class="row mb-4 px-3 pt-4 pb-0 pt-xl-5 pb-xl-4">
                              <img src="<?php
                              if( isset( $company_logo ) ) {
                                if($company_logo != 'superman_sign_in_logo.svg'){
                                  echo base_url() . 'image/company/logo/' . $company_logo;
                                }else{
                                    echo base_url() . 'image/superman_sign_in_logo.svg';
                                }
                              }else{
                                echo base_url() . 'image/superman_sign_in_logo.svg';
                              }
                            ?>" alt="sign up logo" class="img-fluid mx-auto" style="height:78px;">
                           </div>
                           <form method="post" id="superman_sign_in_area" accept-charset="utf-8" enctype="multipart/form-data" onsubmit="superman_sign_in(event);" action="<?php echo base_url(); ?>Superman/Login/login_process" class="form-login">
                              <div class="row" id="filter">
                                 <div class="col-12" id="token">
                                 </div>
                                 <div class="col-12">
                                    <div class="row px-3">
                                       <label class="mb-1">
                                          <h6 class="mb-0 text-sm">Username</h6>
                                       </label>
                                       <input class="mb-4" type="text" name="username" id="username" placeholder="Username Supername">
                                    </div>
                                 </div>
                              </div>
                              <div class="row px-3">
                                 <label class="mb-1">
                                    <h6 class="mb-0 text-sm">Password</h6>
                                 </label>
                                 <input class="mb-4" type="password" name="password" placeholder="Password">
                              </div>
                              <div class="row mb-3 px-3">
                                 <button type="submit" class="btn btn-superman text-center" style="width:100%;font-size: 15px !important;font-weight: bold;">Jadi Superman</button>
                              </div>

                           </form>
                        </div>
                     </div>
                  </div>
                  <div class="bg-superman py-3">
                     <div class="row px-3">
                        <small class="mx-auto mb-2">Copyright &copy; 2021<br>Atra Developed by Malemdiwa Team.</small>
                        <div class="social-contact mx-auto py-2">
                           <span class="fab fa-facebook-f mx-2 text-sm"></span>
                           <span class="fab fa-google-plus-g mx-2 text-sm"></span>
                           <span class="fab fa-linkedin-in mx-2 text-sm"></span>
                           <span class="fab fa-twitter mx-2 text-sm"></span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
