$(document).ready(() => {
    $(".button-collapse").sideNav();
    new Vue({
        el: '#content',
        data: {
            albums: [],
            album: [],
            artists: [],
            artist: [],
            songs: [],
            user: [],
            orderHistory: [],
            search: '',
            loggedin: false,
            showPreviews: false,
            showFavorites: false,
            cartSelected: false,
            checkoutPage: false,
            orderComplete: false,
            showOrderHistory: false,
            favorited: {},
            favorites: [],
            ordered: {},
            cart: [],
            orderCount: 0,
            orderTotal: 0,
            grandTotal: {}
        },
        methods: {
            async fetchAlbums() {
                const data = await $.get('fetchalbums.php');
                this.albums = JSON.parse(data).albums;
                this.artists = JSON.parse(data).artists;
                this.cartSelected = false;
                this.checkoutPage = false;
                this.album = [];
                this.artist = [];
                this.user = [];
                this.orderComplete = false;
                this.showOrderHistory = false;
            },
            async fetchSingleAlbum(id, title) {
                const data = await $.post('fetchalbum.php', {
                    "id": id,
                    "title": title
                });
                this.songs = JSON.parse(data).songs;
                this.album = JSON.parse(data).album;
                this.artist = JSON.parse(data).artist;
                this.cartSelected = false;
                this.checkoutPage = false;
                this.artists = [];
                this.albums = [];
                this.orderComplete = false;
                this.showOrderHistory = false;
            },
            addNewAlbum() {
                const vue = this;
                if (Array.from(new FormData($("#submitalbum")[0]).values()).filter(field => field.length ==
                        0).length > 0) {
                    alert("Error. All fields must be filled in.")
                } else
                    $.post('addalbum.php', $('#submitalbum').serialize(), data => {
                        console.log(data);
                        try {
                            vue.albums.push(JSON.parse(data).albums);
                            vue.artists.push(JSON.parse(data).artists);
                            $('#modal1').modal('close');
                            vue.fetchAlbums();
                        } catch (e) {
                            alert('Error. Invalid album.');
                        }
                    });
            },
            async submitSearch() {
                this.album = [];
                this.artist = [];
                this.user = [];
                this.checkoutPage = false;
                this.cartSelected = false;
                this.orderComplete = false;
                this.showOrderHistory = false;
                if (!this.search == "") {
                    const searchresults = await $.post('searchAlbums.php', {
                        "search": this.search
                    });

                    this.albums = JSON.parse(searchresults).albums;
                } else {
                    this.fetchAlbums();
                }
            },
            async parameterizedSearch(filter, query) {
                this.album = [];
                this.artist = [];
                this.user = [];
                this.cartSelected = false;
                this.checkoutPage = false;
                this.orderComplete = false;
                this.showOrderHistory = false;
                const searchresults = await $.post('searchAlbums.php', {
                    "filter": filter,
                    "search": query
                });
                console.log(searchresults);
                this.albums = JSON.parse(searchresults).albums;
            },
            getAlbumArtist(album) {
                for (let i of this.artists) {
                    if (i.id === album.artist_id) {
                        return i;
                    }
                }
                return false;
            },
            openLoginForm() {
                $('#modal3').modal('open');
            },
            openRegisterForm() {
                $('#modal2').modal('open');
            },
            openForm() {
                $('#modal1').modal('open');
            },
            registerUser() {
                $.post('registeruser.php', $('#submituser').serialize(), data => {
                    if (data === "Error. User already registered.") {
                        alert("Sorry. That username or email is already taken. Please try again.");
                    } else {
                        $('#modal2').modal('close');
                        window.location = 'index.php';
                    }
                });
            },
            login() {
                $.post('login.php', $('#loginform').serialize(), data => {
                    if (data === "failure") {
                        alert('Sorry. Your login information is incorrect. Please try again.');
                    } else {
                        $('#modal3').modal('close');
                        window.location = 'index.php';
                    }
                });
            },
            async loadUserProfile(user) {
                this.albums = [];
                this.artists = [];
                this.user = [];
                this.album = [];
                this.checkoutPage = false;
                this.cartSelected = false;
                this.orderComplete = false;
                this.showOrderHistory = false;
                $.post('userProfile.php', {
                    "username": user
                }, data => {
                    this.user = JSON.parse(data).userInfo;
                });
                $.post('favorite.php',{"user":user}, data => {
                    this.favorites = JSON.parse(data).favorites;
                });
            },
            manageFavorites(artist, album, event) {
                if (!this.favorited[album]) {
                    this.favorited[album] = true;
                } else this.favorited[album] = !this.favorited[album];
                if (this.favorited[album] === true) {
                    event.target.innerHTML = "favorite";
                   $.post('favorite.php', {"addalbum":album,"artist":artist}, data => {
                       this.favorites = JSON.parse(data).favorites;
                   });
                } else {
                    const data = $.post('favorite.php', {"deletealbum":album,"artist":artist});
                    event.target.innerHTML = "favorite_border";
                }
            },
            deleteFavorite(title, artist,event) {
                $(event.target).parent().parent().remove();
                $.post('favorite.php',{"deletealbum":title,"artist":artist});
            },
            fetchFavorites() {
                $.post('favorite.php',{'fetch':true}, data => {
                    try{
                    this.favorites = JSON.parse(data).favorites;
                    } catch(e) {console.log('user not signed in')}
                    this.favorited = {};
                for(let favorite of this.favorites) {
                    this.favorited[favorite.title] = true;
                } 
                });
            },
            manageCart(artist, album, event) {
                if (!this.ordered[album]) {
                    this.ordered[album] = true;
                } else this.ordered[album] = !this.ordered[album];
                if (this.ordered[album] === true) {
                    event.target.innerHTML = "remove_shopping_cart";
                   $.post('orders.php', {"addalbum":album,"artist":artist}, data => {
                       this.cart = JSON.parse(data).orders;
                       this.orderCount = JSON.parse(data).orderCount;
                   });
                } else {
                    const data = $.post('orders.php', {"deletealbum":album,"artist":artist}, data => {
                        this.cart = JSON.parse(data).orders;
                        this.orderCount = JSON.parse(data).orderCount;
                    });
                    event.target.innerHTML = "add_shopping_cart";
                }
            },
            fetchOrders() {
                $.post('orders.php',{'fetch':true}, data => {
                    try{
                    this.cart = JSON.parse(data).orders;
                    this.orderCount = JSON.parse(data).orderCount;
                    } catch(e) {console.log('user not signed in')}
                    this.ordered = {};
                for(let order of this.cart) {
                    this.ordered[order.title] = true;
                } 
                });
            },
            showOrders() {
                this.orderTotal = 0;
                this.albums = [];
                this.artists = [];
                this.user = [];
                this.album = [];
                this.checkoutPage = false;
                this.cartSelected = true;
                this.orderComplete = false;
                this.showOrderHistory = false;
                for(let item of this.cart) {
                    this.orderTotal += Number(item.price);
                }
            },
            deleteOrder(title, artist,event) {
                $(event.target).parent().parent().remove();
                $.post('orders.php',{"deletealbum":title,"artist":artist}, data => {
                    this.cart = JSON.parse(data).orders;
                    this.orderCount = JSON.parse(data).orderCount;
                });
            },
            changeQuantity(operation,item) {
                
                if(operation === "add") { 
                    this.orderTotal += parseFloat(item.price);
                    item.quantity++;
                } else {
                    if((this.orderTotal - parseFloat(item.price)) >= 0 && item.quantity > 1) {
                        this.orderTotal -= parseFloat(item.price);
                        item.quantity--;
                    }
                } 
                $.post('orders.php',{"updatePriceQuantity":item,"price":this.orderTotal,"quantity":item.quantity});
            },
            changeFormat(item,event) {
                switch(event.target.value) {
                    case "digital":
                        if(item.format === "vinyl") {
                            this.orderTotal -= 8;
                            item.price -= 8;
                        } else if(item.format === "cd") {
                            this.orderTotal -= 5;
                            item.price -= 5;
                        }
                        break;
                    case "cd":
                        if(item.format === "digital") {
                            this.orderTotal += 5;
                            item.price += 5;
                        } else if(item.format === "vinyl") {
                            this.orderTotal -= 5;
                            item.price -= 5;
                        }
                        break;
                    case "vinyl":
                        if(item.format === "cd") {
                            this.orderTotal += 5;
                            item.price += 5;
                        } else if(item.format === "digital") {
                            this.orderTotal += 8;
                            item.price += 8;
                        }
                        break;
                }
                item.format = event.target.value;
                $.post('orders.php',{"updateFormat":item});
            }, 
            showCheckout() {
                this.cartSelected = false;
                this.checkoutPage = true;
                this.getGrandTotal();
            },
            getGrandTotal() {
                let shippingCost = 0;
                for(var item of this.cart) {
                    if(item.format === "cd") {
                        shippingCost += (2 * item.quantity);
                    } else if (item.format === "vinyl") {
                        shippingCost += (5 * item.quantity);
                    }
                }
                const tax = this.orderTotal * (9.25/100);
                this.grandTotal = {"tax":tax.toFixed(2),shipping:shippingCost,"grandTotal":(this.orderTotal + tax + shippingCost).toFixed(2)};
            },
            completeOrder() {
                this.orderTotal = 0;
                this.albums = [];
                this.artists = [];
                this.user = [];
                this.album = [];
                this.checkoutPage = false;
                this.cartSelected = false;
                this.showOrderHistory = false;
                this.orderComplete = true;
                $.post('orders.php',{"ordercomplete":true,"orders":this.cart,"total":this.grandTotal}, data => {
                    console.log(data);
                });
                setTimeout(function(){
                    window.location = "index.php";
                },5000);
            },
            fetchOrderHistory() {
                this.showOrderHistory = !this.showOrderHistory;
                if(this.showOrderHistory)
                $.post('orders.php',{"orderhistory":true}, data => {      
                    this.orderHistory = JSON.parse(data).history;
                });
            }
        },
        computed: {
            singleAlbumSelected() {
                return this.album.length > 0 && this.artist.length > 0;
            },
            userProfileSelected() {
                return this.user.length > 0;
            },
            orderCompletePage() {
                return this.albums === [] && this.album === [] && this.checkoutPage === false && this.cartSelected === false && this.checkoutPage === true;
            }
        },
        created: function () {
            this.fetchAlbums();
            this.fetchOrders();
            this.fetchFavorites();
        }
    });
});