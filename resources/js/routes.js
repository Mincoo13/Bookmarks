
import Hello from './views/Hello'
import Home from './views/Home'
import Login from './views/Login'
import Register from './views/Register'
import Profile from './views/Profile'
import EditProfile from './views/EditProfile'
import EditPassword from './views/EditPassword'
import Users from './views/Users'



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
        {
            path: '/profile',
            name: 'profile',
            component: Profile,
            meta: {
                auth: true
            }
        },
        {
            path: '/edit-profile',
            name: 'edit-profile',
            component: EditProfile,
            meta: {
                auth: true
            }
        },
        {
            path: '/edit-password',
            name: 'edit-password',
            component: EditPassword,
            meta: {
                auth: true
            }
        },
        {
            path: '/users',
            name: 'users',
            component: Users,
            meta: {
                auth: true
            }
        },


    ];