import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  },
  {
    path: '/padres',
    name: 'HomePadres',
    component: () => import('../views/Padres/HomePadres.vue')
  },
  {
    path: '/padres/registro',
    name: 'RegistroPadres',
    component: () => import('../views/Padres/RegistroPadres.vue')
  },
  {
    path: '/padre-con-hijos/:dniPadre',
    name: 'RelacionPadres',
    component: () => import('../views/Padres/RegistroPadres.vue')
  },
	{
    path: '/alumnos',
    name: 'HomeAlumnos',
    component: () => import('../views/Alumnos/HomeAlumnos.vue')
  },
	{
    path: '/reuniones',
    name: 'HomeReuniones',
    component: () => import('../views/Reuniones/HomeReuniones.vue')
  },
	{
    path: '/reunion/:idReunion/detallado',
    name: 'DetalleReunion',
    component: () => import('../views/Reuniones/DetalleReunion.vue')
  },
	{
    path: '/deudas',
    name: 'HomeDeudas',
    component: () => import('../views/Deudas/HomeDeudas.vue')
  },
	{
    path: '/deuda/:idDeuda/detallado',
    name: 'DetalleDeudas',
    component: () => import('../views/Deudas/DetalleDeudas.vue')
  },
	{
    path: '/reportes',
    name: 'HomeReportes',
    component: () => import('../views/Reportes/HomeReportes.vue')
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
