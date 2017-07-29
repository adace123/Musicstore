</div>
<div style="text-align:center" id="modal1" class="modal">
    <div class="modal-content">
        <h3>Enter the Album Information</h3>
        <div class="row">
            <form class="col s12" id="submitalbum" method="post" action="addalbum.php">
                <div class="row">
                    <div class="input-field col s6">
                        <input name="artist" placeholder="Artist" type="text">

                    </div>
                    <div class="input-field col s6">
                        <input name="title" placeholder="Title" type="text">

                    </div>
                </div>

                <div class="row">
                    <button @click.prevent="addNewAlbum" type="submit" name="submit" class="waves-effect waves-light btn">Submit</button>
                </div>
            </form>
        </div>


    </div>

</div>

<div style="text-align:center" id="modal3" class="modal">
    <div class="modal-content">
        <h3>Login</h3>
        <div class="row">
            <form class="col s12" id="loginform" method="post" action="login.php">
                <div class="row">
                    <div class="input-field col s6">
                        <input name="username" placeholder="Username" type="text" required>

                    </div>
                    <div class="input-field col s6">
                        <input name="password" placeholder="Password" type="password" required>

                    </div>
                </div>

                <div class="row">
                    <button @click.prevent="login" type="submit" name="submit" class="waves-effect waves-light btn">Submit</button>
                </div>
            </form>
        </div>


    </div>

</div>

<div style="text-align:center" id="modal2" class="modal">
    <div class="modal-content">
        <h3>Please enter a username, email and password.</h3>
        <div class="row">
            <form class="col s12" id="submituser" method="post" action="registeruser.php">
                <div class="row">
                    <div class="input-field col s12">
                        <input name="username" placeholder="Username" required>
                    </div>

                    <div class="input-field col s12">
                        <input name="email" placeholder="Email" type="email" required>
                    </div>

                    <div class="input-field col s12">
                        <input name="password" placeholder="Password" type="password" required>
                    </div>

                    <div class="row">
                        <button @click.prevent="registerUser" type="submit" name="submit" class="waves-effect waves-light btn">Submit</button>
                    </div>
            </form>
            </div>


        </div>

    </div>

</div>
</body>

</html>