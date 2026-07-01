import React, { useEffect, useState } from 'react';
import { Navigate } from 'react-router-dom';
import api from '../../services/api';

interface Props {
    children: React.ReactNode;
}

const AuthGuard: React.FC<Props> = ({ children }) => {
    const [valid, setValid] = useState<boolean | null>(null);
    const token = localStorage.getItem('token');

    useEffect(() => {
        if (!token) {
            setValid(false);
            return;
        }

        api.get('/me')
            .then(() => setValid(true))
            .catch(() => {
                localStorage.removeItem('token');
                setValid(false);
            });
    }, [token]);

    if (valid === null) {
        return <div className="flex items-center justify-center min-h-screen">Cargando...</div>;
    }

    if (!valid) {
        return <Navigate to="/login" replace />;
    }

    return children;
};

export default AuthGuard;
