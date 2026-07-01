import React, { useState, useEffect } from 'react';
import api from '../services/api';
import Layout from '../components/layout/Layout';

interface DashboardStats {
    socios: number;
    medidores: number;
    pagos: number;
    facturas: number;
}

interface CardItem {
    title: string;
    value: number;
    icon: string;
    color: string;
}

const Dashboard: React.FC = () => {
    const [stats, setStats] = useState<DashboardStats>({
        socios: 0,
        medidores: 0,
        pagos: 0,
        facturas: 0
    });

    useEffect(() => {
        fetchStats();
    }, []);

    const fetchStats = async (): Promise<void> => {
        try {
            const [sociosRes, medidoresRes, pagosRes, facturasRes] = await Promise.all([
                api.get('/socios'),
                api.get('/medidores'),
                api.get('/pagos'),
                api.get('/facturas')
            ]);

            setStats({
                socios: sociosRes.data.data?.length ?? 0,
                medidores: medidoresRes.data.data?.length ?? 0,
                pagos: pagosRes.data.data?.length ?? 0,
                facturas: facturasRes.data.data?.length ?? 0
            });
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    };

    const cards: CardItem[] = [
        { title: 'Socios', value: stats.socios, icon: '👥', color: 'bg-blue-500' },
        { title: 'Medidores', value: stats.medidores, icon: '📊', color: 'bg-green-500' },
        { title: 'Pagos', value: stats.pagos, icon: '💰', color: 'bg-yellow-500' },
        { title: 'Facturas', value: stats.facturas, icon: '📄', color: 'bg-purple-500' },
    ];

    return (
        <Layout>
            <h1 className="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {cards.map((card, index) => (
                    <div key={index} className="bg-white rounded-lg shadow p-6">
                        <div className="flex items-center">
                            <div className={`${card.color} text-white p-3 rounded-full text-2xl`}>
                                {card.icon}
                            </div>
                            <div className="ml-4">
                                <p className="text-sm text-gray-500">{card.title}</p>
                                <p className="text-2xl font-bold">{card.value}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            <div className="mt-8 bg-white rounded-lg shadow p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4">Últimas Actividades</h2>
                <div className="text-gray-500">
                    Aquí puedes mostrar las últimas lecturas o movimientos
                </div>
            </div>
        </Layout>
    );
};

export default Dashboard;
