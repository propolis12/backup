<template >
  <div>
    <!--<nav :class="[$style.navbar, 'navbar-expand-md', 'navbar-light', 'bg-light' ]">-->
      <nav class="navbar navbar-expand-md navbar-light bg-light ">
      <a class="navbar-brand" href="#"><p class="text-white text-center">{{ title }}</p></a>
      <button class="navbar-toggler  collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class=" collapse navbar-collapse  " id="navbarSupportedContent">
        <div class="row " >
          <ul class="navbar-nav  d-flex flex-row mx-md-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#"><p class="text-white">Home</p><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#">Disabled</a>
            </li>


          </ul>
        </div>

        <!--<button class="navbar-toggler  collapsed" type="button" data-toggle="collapse" data-target="#endHalfNavbar" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>-->

        <ul id="endHalfNavbar" class="collapse navbar-collapse navbar-nav justify-content-end">
          <li id="js-nav-updates">
            <a class="nav-link navbar-navlink nav-bell mt-3 mr-4" href="#">
              <i class="fa fa-bell"></i>
              <div class="alert-circle"></div>
            </a>
          </li>
          <li><a class="nav-link mt-3 mr-4" href="#">Tutorials</a></li>
          <li class="dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" title="Account Menu" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">
              <img class="user-img" src="https://image.flaticon.com/icons/png/512/61/61205.png">
              <i class="fa fa-chevron-down pl-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="#">My Account</a>
              <a class="dropdown-item" href="#">My Bookmarks</a>
              <a class="dropdown-item" href="#">Sign Out</a>

            </div>
          </li>
        </ul>

        <!-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
         <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>-->
      </div>

    </nav>
  </div>
</template>


<script>
export default {
  name: 'Navbar',
  props: {
    title: {
      type: String,
      required: true,
    }
  }

};



</script>


<style>



</style>