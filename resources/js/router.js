import VueRouter from 'vue-router';

import Vue from 'vue';
Vue.use(VueRouter);

import Home from './components/Home';
import Login from './components/Login';
import Register from './components/Register';
import SingleProduct from './components/SingleProduct';
import Checkout from './components/Checkout';
import Cart from './components/Cart';
import UserBoard from './components/UserBoard';
import Admin from './components/Admin';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            props: (route) => ({prev : route.query.prev})
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            props: (route) => ({prev : route.query.prev})
        },
        {
            path: '/products/:id/:detailsId',
            name: 'single-products',
            component: SingleProduct
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: Checkout,
            props: (route) => ({pid : route.query.pid})
        },
        {
            path: '/cart',
            name: 'cart',
            component: Cart,
            props: true
        },
        /*{
            path: '/bancontact/land',
            name: 'bancontact.land',
            component: BancontactLand
        },*/
        {
            path: '/userboard',
            name: 'userboard',
            component: UserBoard,
            meta: { 
                requiresAuth: true,
                is_user : true
            }
        },
        {
            path: '/userboard/:page',
            name: 'userboard-pages',
            component: UserBoard,
            props: (route) => ({id : route.query.id}),
            meta: {
                requiresAuth: true,
                is_user : true
            }
        },
        {
            path: '/admin',
            name: 'admin',
            component: Admin,
            meta: { 
                requiresAuth: true,
                is_admin : true
            }
        },
        {
            path: '/admin/:page',
            name: 'admin-pages',
            component: Admin,
            meta: { 
                requiresAuth: true,
                is_admin : true
            }
        },  
    ],
});

router.beforeEach((to, from, next) => {
   if (to.matched.some(record => record.meta.requiresAuth)) {
        if (localStorage.getItem('jwt') == null) { // if you are not logged in, redirect to login
            next({
                path: '/login',
                params: { nextUrl: to.fullPath }
            });
        } else { // logged in
            let user = JSON.parse(localStorage.getItem('user'));

            if (to.matched.some(record => record.meta.is_admin)) {
                if (user.is_admin == 1) {
                    next();
                } else {
                    next({ name: 'userboard'});
                }
            } else if (to.matched.some(record => record.meta.is_user)) {
                if (user.is_admin == 0) {
                    next();
                } else {
                    next({ name: 'admin'});
                }
            }

            next();
        }
    } else {
        next();
    }
});

export default router;