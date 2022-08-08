import { createWebHistory, createRouter } from "vue-router";

import Register from '../components/Register';
import Login from '../components/Login';
import Dashboard from '../components/Dashboard';

import Posts from '../components/Post/List';
import AddPost from '../components/Post/Add';
import EditPost from '../components/Post/Edit';

import User from '../components/user/List';
import AddUser from '../components/user/Add';
import EditUser from '../components/user/Edit';

export const routes = [
    { 
        path: '/', 
        redirect: { name: 'login' } 
    },
    {
        name: 'register',
        path: '/register',
        component: Register
    },
    {
        name: 'login',
        path: '/login',
        component: Login
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard
    },
    {
        name: 'post',
        path: '/post',
        component: Posts
    },
    {
        name: 'addpost',
        path: '/post/add',
        component: AddPost
    },
    {
        name: 'editpost',
        path: '/post/edit/:id',
        component: EditPost
    },
    {
        name: 'user',
        path: '/user',
        component: User
    },
    {
        name: 'adduser',
        path: '/user/add',
        component: AddUser
    },
    {
        name: 'edituser',
        path: '/user/edit/:id',
        component: EditUser
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router;