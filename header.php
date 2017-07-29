<?php session_start();?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Album Explorer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src='https://code.jquery.com/jquery-3.2.1.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/vue/2.3.4/vue.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/materialize/0.99.0/js/materialize.min.js'></script>
    <script src="musicdatabase.js?2"></script>

    <link rel="stylesheet" href="styles.css">
</head>

<body>
    
    <div class="container" id="content">
        <nav>
            <div class="nav-wrapper">

                <form class="left" @submit.prevent>
                    <input id="search" type="text" v-model.trim="search" @input="submitSearch" placeholder="Search by artist, title or genre">
                    <i class="material-icons">search</i>
                </form>


                <a href="#" class="brand-logo center">
                    <h5 onclick="window.location = 'index.php'" style="font-weight:800;color:white;">ALBUM EXPLORER</h5>
                </a>
               
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <?php if(!isset($_SESSION['loggedin'])) { ?>
                    <li><a @click="openRegisterForm">Register</a></li>
                    <li><a @click="openLoginForm">Login</a></li>
                    <?php } else { ?>
                    <li><a @click="openForm">Add an Album</a></li>
                    <li><a @click="loadUserProfile('<?php echo $_SESSION['username'];?>')"><?php echo $_SESSION['username']; ?></a></li>
                    <li><i @click="showOrders" style="line-height:100%;margin-top:40%;" class="material-icons">shopping_cart</i><label>{{orderCount}}</label></li>
                    <li><a href="logout.php">Logout</a></li>
                    <?php }  ?>
                </ul>

                <ul class="side-nav" id="mobile-demo">
                    <li><a @click="openForm" href="#modal1" class="modal-trigger open-modal">Add an Album</a></li>
                    <li><a @click="openRegisterForm">Register</a></li>
                    <li><a>Logout</a></li>
                </ul>

            </div>
        </nav>