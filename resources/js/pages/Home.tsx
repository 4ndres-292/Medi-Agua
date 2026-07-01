import React from 'react';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/layout/Layout';

const Home: React.FC = () => {
    const navigate = useNavigate();

    return (
        <Layout>
            <div className="flex flex-col items-center justify-center text-center py-20 px-6">

                <h1 className="text-4xl md:text-6xl font-bold text-blue-700">
                    💧 Medi_Agua
                </h1>

                <p className="mt-4 text-lg md:text-xl text-gray-600 max-w-2xl">
                    Sistema Integral de Gestión de Agua Potable
                </p>

                <p className="mt-6 text-gray-500 max-w-3xl leading-relaxed">
                    Administre socios, medidores, lecturas, facturación y pagos
                    desde una sola plataforma centralizada, moderna y eficiente.
                </p>

                <div className="mt-10">
                    <button
                        onClick={() => navigate('/login')}
                        className="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                    >
                        Iniciar sesión
                    </button>
                </div>

            </div>
        </Layout>
    );
};

export default Home;