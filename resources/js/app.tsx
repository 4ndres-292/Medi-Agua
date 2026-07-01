import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import AuthGuard from './components/auth/AuthGuard';
import Login from './components/auth/Login';
import Home from './pages/Home';
import Dashboard from './pages/Dashboard';
import Users from './pages/users/Users';
import Roles from './pages/roles/Roles';
import Socios from './pages/socios/Socios';
import Medidores from './pages/medidores/Medidores';
import Lecturas from './pages/lecturas/Lecturas';
import Tarifas from './pages/tarifas/Tarifas';
import Facturas from './pages/facturas/Facturas';
import Pagos from './pages/pagos/Pagos';
import Notificaciones from './pages/notificaciones/Notificaciones';
import Ingresos from './pages/reportes/Ingresos';
import Deudores from './pages/reportes/Deudores';
import Consumo from './pages/reportes/Consumo';
import Perfil from './pages/usuario/Perfil';
import Configuracion from './pages/usuario/Configuracion';

const container = document.getElementById('app');

if (!container) {
    throw new Error("No se encontró el elemento con id 'app'");
}

const root = ReactDOM.createRoot(container);

root.render(
    <React.StrictMode>
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
                <Route path="/dashboard" element={<AuthGuard><Dashboard /></AuthGuard>} />

                {/* Administración */}
                <Route path="/users" element={<AuthGuard><Users /></AuthGuard>} />
                <Route path="/roles" element={<AuthGuard><Roles /></AuthGuard>} />
                <Route path="/socios" element={<AuthGuard><Socios /></AuthGuard>} />
                <Route path="/medidores" element={<AuthGuard><Medidores /></AuthGuard>} />
                <Route path="/lecturas" element={<AuthGuard><Lecturas /></AuthGuard>} />
                <Route path="/tarifas" element={<AuthGuard><Tarifas /></AuthGuard>} />
                <Route path="/facturas" element={<AuthGuard><Facturas /></AuthGuard>} />
                <Route path="/pagos" element={<AuthGuard><Pagos /></AuthGuard>} />
                <Route path="/notificaciones" element={<AuthGuard><Notificaciones /></AuthGuard>} />

                {/* Reportes */}
                <Route path="/reportes/ingresos" element={<AuthGuard><Ingresos /></AuthGuard>} />
                <Route path="/reportes/deudores" element={<AuthGuard><Deudores /></AuthGuard>} />
                <Route path="/reportes/consumo" element={<AuthGuard><Consumo /></AuthGuard>} />

                {/* Usuario */}
                <Route path="/perfil" element={<AuthGuard><Perfil /></AuthGuard>} />
                <Route path="/configuracion" element={<AuthGuard><Configuracion /></AuthGuard>} />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    </React.StrictMode>
);
