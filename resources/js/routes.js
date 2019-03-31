
import Hello from './views/Hello'
import Home from './views/Home'
import Login from './views/Login'
import Register from './views/Register'


export const routes =[
        {
            path: '/',
            name: 'home',
            component: Home,
            meta: {
                auth: true
            }
        },
        {
            path: '/hello',
            name: 'hello',
            component: Hello,
            meta: {
                auth: false
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                auth: false
            }
        },
        {
            path: '/register-user',
            name: 'register-user',
            component: Register,
            meta: {
                auth: false
            }
        },

    ];