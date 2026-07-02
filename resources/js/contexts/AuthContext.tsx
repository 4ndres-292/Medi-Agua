import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import api from '../services/api';

interface User {
    id: number;
    username: string;
    lastname: string;
    email: string;
    role?: {
        id: number;
        name: string;
    };
}

interface AuthContextType {
    user: User | null;
    token: string | null;
    loading: boolean;
    login: (email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
    isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = (): AuthContextType => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

interface AuthProviderProps {
    children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null);
    const [token, setToken] = useState<string | null>(localStorage.getItem('token'));
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const initAuth = async () => {
            if (!token) {
                setLoading(false);
                return;
            }

            try {
                const response = await api.get('/me');
                setUser(response.data.data);
            } catch (error) {
                localStorage.removeItem('token');
                setToken(null);
                setUser(null);
            } finally {
                setLoading(false);
            }
        };

        initAuth();
    }, [token]);

    const login = async (email: string, password: string): Promise<void> => {
        const response = await api.post('/login', { email, password });
        const { user: userData, token: authToken } = response.data.data;

        localStorage.setItem('token', authToken);
        setToken(authToken);
        setUser(userData);
    };

    const logout = async (): Promise<void> => {
        try {
            await api.post('/logout');
        } catch (error) {
            // Ignorar error, limpiar de todas formas
        } finally {
            localStorage.removeItem('token');
            setToken(null);
            setUser(null);
        }
    };

    const value: AuthContextType = {
        user,
        token,
        loading,
        login,
        logout,
        isAuthenticated: !!token && !!user,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};
