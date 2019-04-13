
import Home from './views/Home'
import Login from './views/Login'
import Profile from './views/Profile'
import EditProfile from './views/EditProfile'
import EditPassword from './views/EditPassword'
import Users from './views/Users'
import ResetPassword from './views/ResetPassword'
import UsersProfile from './views/UsersProfile'
import SearchBookmarks from './views/SearchBookmarks'
import Categories from './views/Categories'
import Bookmark from './components/Bookmark'
import EditBookmark from './components/EditBookmark'
import BookmarkLists from './views/BookmarkLists'
import BookmarkListsDetail from './views/BookmarkListsDetail'

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
            path: '/login',
            name: 'login',
            component: Login,
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
        {
            path: '/reset-password',
            name: 'reset-password',
            component: ResetPassword,
            meta: {
                auth: false
            }
        },
        {
            path: '/users/:id',
            name: 'users-profile',
            component: UsersProfile,
            meta: {
                auth: true
            }
        },
        {
            path: '/search-bookmarks',
            name: 'search-bookmarks',
            component: SearchBookmarks,
            meta: {
                auth: true
            }
        },
        {
            path: '/bookmark/:id',
            name: 'bookmark',
            component: Bookmark,
            meta: {
                auth: true
            }
        },
        {
            path: '/bookmark/:id/edit',
            name: 'edit-bookmark',
            component: EditBookmark,
            meta: {
                auth: true
            }
        },
        {
            path: '/categories',
            name: 'categories',
            component: Categories,
            meta: {
                auth: true
            }
        },
        {
            path: '/bookmark-lists',
            name: 'bookmark-lists',
            component: BookmarkLists,
            meta: {
                auth: true
            }
        },
        {
            path: '/bookmark-lists/:id',
            name: 'bookmark-lists-detail',
            component: BookmarkListsDetail,
            meta: {
                auth: true
            }
        },
    ];