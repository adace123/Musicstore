    <?php include_once('header.php'); ?>
    <!--
        Thanks for checking out my site!
        If you experience any bugs and/or have suggestions, please let me know.
        Note: The prices at the checkout page are currently not working correctly. 
        Note: There is currently no way to upload user profile images. 
        I plan to fix both of these soon.

        Future features:
        1. Only an admin can add albums, not a regular user -> include ability to delete albums, add quantity in stock for cd and vinyl to album table. (planned)
        2. User comments and/or reviews for albums + overall average rating for albums. (planned)
        3. Customer support chat (maybe)
        4. Errors are flash messages instead of alerts. (planned)
        5. Filter option in navbar i.e. price, artist, genre etc. (planned)
        6. Facebook and Google Oauth sign-in/registration (maybe)
        7. Shipping rates are based on user's address and item weight instead of hard-coded. -> connect with Shippo API (planned)
        8. Order confirmation page connects to Paypal Sandbox (maybe)
        9. API endpoints for album info and list of registered users (maybe)
        10. Newsletter subscription option to let users know about new features and sales(?). (maybe)
        11. improve mobile responsiveness (planned)
        12. use AmplitudeJS instead of default audio player (maybe)
        14. add date ordered to order history
    -->
        <div class="row">
            <div style="cursor:pointer" v-if="!singleAlbumSelected" class="col xl3 l4 m6 s12" v-for="album in albums" @click="fetchSingleAlbum(album.artist_id, album.title)">   
                <div class="overlay">
                    <div class="overlay-content">
                        <h4>{{ album.title }}</h4>
                        <h6>By {{ getAlbumArtist(album).name }}</h6>
                        <h4>${{album.price}}</h4>
                        <?php if(isset($_SESSION['username'])) {?>
                        <h3 style="margin-left:15%;position:fixed;"><i style="position:absolute" @click.stop="manageFavorites(getAlbumArtist(album).name,album.title,$event)" class="material-icons">{{favorited[album.title] === true ? 'favorite' : 'favorite_border'}}</i></h3>
                        <h3 style="margin-left:11%;position:fixed;"><i style="position:absolute" @click.stop="manageCart(getAlbumArtist(album).name,album.title,$event)"  class="material-icons">{{ordered[album.title] === true ? 'remove_shopping_cart' : 'add_shopping_cart'}}</i></h3>
                        <?php } ?>
                    </div>
                </div>
            <img v-if="album.image_url" :src="album.image_url">
            <img v-else src="notfound.png">
        </div>

        <div class="card" v-if="cartSelected" style="width:45%;margin:0 auto;background-color:#f5f5f5;text-align:center">

            <div class="card-content">
                <h2 style="color:purple;">Your Items</h2>
            </div>

            <ul id="orders" class="collection">
                <li v-if="cart.length === 0">Nothing in your cart.</li>
                <li class="collection-item avatar" v-for="item in cart">
                    <img class="circle" :src="item.thumbnail">
                    <h6 style="padding-bottom:5px" class="title">{{item.title}} </h6>
                    <h6 style="padding-bottom:5px">{{item.artist}}</h6><hr width="150px">
                    <h6 >Quantity:   <strong style="padding-left:10px;">{{item.quantity}}</strong> 
                        <a @click="changeQuantity('minus',item)"><button style="background:none;border:none;">-</button></a> | 
                        <a @click="changeQuantity('add',item)"><button style="background:none;border:none;">+</button></a><br>
                        <span>Format: <input  @change="changeFormat(item,$event)" :name="item.username+item.artist+item.title" :id="item.username+item.artist+item.title+'digital'" value="digital" checked type="radio"><label :for="item.username+item.artist+item.title+'digital'" class="format-label">digital</label>
                        <input  @change="changeFormat(item,$event)" :name="item.username+item.artist+item.title" :id="item.username+item.artist+item.title+'cd'" value="cd" type="radio"><label :for="item.username+item.artist+item.title+'cd'" class="format-label">cd</label>
                        <input @change="changeFormat(item,$event)" :name="item.username+item.artist+item.title" :id="item.username+item.artist+item.title+'vinyl'" value="vinyl" type="radio"><label :for="item.username+item.artist+item.title+'vinyl'" class="format-label">vinyl</label>
                        </span>
                    </h6>
                    <span class="secondary-content">${{item.price}} 
                    </span>
                    
                    <a @click="deleteOrder(item.title,item.artist,$event)" class="secondary-content"><i style="margin-top:60%;padding-left:60%;height:60px;width:60px;" class="material-icons">delete</i></a>
                </li>
            </ul>

            <div class="card-action">
                <h3 v-if="cart.length > 0">Subtotal: ${{orderTotal}}</h3>
                <h3 v-else>Subtotal: $0</h3>
                <span>
                    <a href="index.php">Back to main page</a>
                    <a style="cursor:pointer" @click="showCheckout">Checkout</a>
                </span>
            </div>
        </div><br>

        <div class="card" v-if="checkoutPage">
            <div class="card-content">
                <h3>Your grand total is: ${{grandTotal['grandTotal']}}</h3>
                <h6>This includes ${{grandTotal['tax']}} tax and ${{grandTotal['shipping']}} shipping.</h6>
            </div>
            <div class="card-action">
                <span>
                    <a href="index.php">Back to main page</a>
                    <a href="#" @click="showOrders">Back to cart</a>
                    <a href="#" @click="completeOrder">Complete order</a>
                </span>
            </div>
        </div>

        <div class="card" v-if="orderComplete">
            <div class="card-content">
                <h3>Thank you for your order!</h3>
                <h4>You should receive an order confirmation email shortly.</h4>
                <h6>You will be redirected to the main page shortly...</h6>
            </div>
        </div>

        <div class="card" v-if="userProfileSelected" style="width:45%;margin:0 auto;background-color:#f5f5f5;text-align:center">
                <div class="card-image">
                <img style="margin:0 auto;padding-top:10px;" :src="user[0].image_url" alt="notfound.png">
                </div>

            <div class="card-content">
                <h2 style="color:purple;">{{user[0].username}}</h2>
                <h4>{{user[0].email}}</h4>
            </div>

            <div class="card-action">
                <span>
                    <a href="index.php">Back to main page</a>
                    <a @click="showFavorites = !showFavorites" href="#">Favorites</a>
                    <a @click="fetchOrderHistory" href="#">Order History</a>
                </span>
            </div>
        
            <ul id="favorites" v-if="showFavorites" class="collection">
                <li v-if="favorites.length === 0">No favorites yet.</li>
                <li class="collection-item avatar" v-for="favorite in favorites">
                    <img class="circle" :src="favorite.thumbnail">
                    <span class="title">{{favorite.title}} </span>
                    <p>{{favorite.artist}}</p>
                    <a @click="deleteFavorite(favorite.title,favorite.artist,$event)" class="secondary-content"><i style="margin-top:60%;height:60px;width:60px;" class="material-icons">delete</i></a>
                </li>
            </ul>

            <ul id="orderhistory" v-if="showOrderHistory" class="collection">
                <li v-if="orderHistory.length === 0">No orders yet.</li>
                <li class="collection-item avatar" v-for="order in orderHistory">
                    <img class="circle" :src="order.thumbnail">
                    <span class="title">{{order.title}} </span>
                    <p>{{order.artist}}</p>
                    <span class="secondary-content">
                        <h6>${{order.price}}</h6>
                        <h6>Format: {{order.format}}</h6>
                        <h6>Quantity: {{order.quantity}}</h6>
                    </span>
                </li>
            </ul>
            </div>

            
            <div class="card" v-if="singleAlbumSelected" style="width:45%;margin:0 auto;background-color:#f5f5f5;text-align:center">
                <div class="card-image">
                <img style="margin:0 auto;padding-top:10px;" :src="album[0].image_url">
                </div>

            <div class="card-content">
                <h3>{{album[0].title}}</h3>
                <p style="font-size:20px;"><a href="#" @click="parameterizedSearch('name',artist[0].name)">{{artist[0].name}}</a></p>
                <h6>Release Date: {{album[0].released}}</h6>
                <h6>Genre: <a href="#" @click="parameterizedSearch('genre',album[0].genre)">{{album[0].genre}}</a></h6>
            </div>

            <div class="card-action">
                <span>
                    <a href="index.php">Back to main page</a>
                    <a @click="showPreviews = !showPreviews" href="#">Show Song Previews</a>
                </span>
            </div>

            <ul v-if="showPreviews" class="collection">
                <li class="collection-item" v-for="song in songs">
                    <h6>{{song.track_num}}. {{song.song_title}}</h6>
                    <p></p>
                    <audio :src="song.audio_url" controls>
                    </audio>
                </li>
            </ul>
            
        </div>


    <?php include_once('footer.php'); ?>