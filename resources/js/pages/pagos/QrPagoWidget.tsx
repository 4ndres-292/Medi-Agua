import React, { useState, useEffect } from 'react';
import api from '../../services/api';

interface QrData {
    id: number;
    imagen_url: string;
    fecha_actualizacion: string;
    valido_hasta: string | null;
    vencido: boolean;
}

const QrPagoWidget: React.FC = () => {
    const [qr, setQr] = useState<QrData | null>(null);
    const [esAdmin, setEsAdmin] = useState(false);
    const [archivo, setArchivo] = useState<File | null>(null);
    const [validoHasta, setValidoHasta] = useState('');
    const [subiendo, setSubiendo] = useState(false);
    const [error, setError] = useState('');

    const cargarQr = () => {
        api.get('/qr-pago')
            .then(res => setQr(res.data.data))
            .catch(() => setQr(null));
    };

    useEffect(() => {
        cargarQr();

        api.get('/me')
            .then(res => {
                const usuario = res.data?.data;
                const rol = usuario?.rol;

                // El rol viene como objeto (ej: { id: 1, nombre: 'Administrador' }),
                // así que buscamos el nombre en los campos más comunes.
                // Como respaldo, si nada de eso calza, buscamos el texto
                // "Administrador" en cualquier parte del objeto rol.
                const nombreRol =
                    (typeof rol === 'string' ? rol : null) ??
                    rol?.nombre ??
                    rol?.nombre_rol ??
                    rol?.name ??
                    '';

                const esAdministrador =
                    nombreRol === 'Administrador' ||
                    JSON.stringify(rol ?? '').includes('Administrador');

                console.log('Usuario /me:', usuario, '→ rol detectado:', nombreRol || rol);
                setEsAdmin(esAdministrador);
            })
            .catch(() => setEsAdmin(false));
    }, []);

    const handleSubir = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');

        if (!archivo) {
            setError('Selecciona una imagen del QR.');
            return;
        }

        const formData = new FormData();
        formData.append('imagen', archivo);
        if (validoHasta) {
            formData.append('valido_hasta', validoHasta);
        }

        setSubiendo(true);
        try {
            await api.post('/qr-pago', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            setArchivo(null);
            setValidoHasta('');
            cargarQr();
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message ?? 'Error al subir el QR.');
        } finally {
            setSubiendo(false);
        }
    };

    return (
        <div className="bg-white rounded-lg shadow p-6 mb-6 max-w-sm">
            <h2 className="text-lg font-bold text-gray-800 mb-3">QR para pagos</h2>

            {qr ? (
                <>
                    <img
                        src={qr.imagen_url}
                        alt="QR de pago"
                        className="w-full rounded-lg border"
                    />
                    <p className="text-xs text-gray-500 mt-2">
                        Actualizado el {qr.fecha_actualizacion}
                        {qr.valido_hasta && ` — válido hasta ${qr.valido_hasta}`}
                    </p>
                    {qr.vencido && (
                        <div className="mt-2 bg-yellow-50 text-yellow-800 text-xs px-3 py-2 rounded">
                            ⚠️ Este QR ya venció. Verifica que siga siendo válido antes de pagar.
                        </div>
                    )}
                </>
            ) : (
                <p className="text-sm text-gray-500">Aún no se ha subido ningún QR.</p>
            )}

            {esAdmin && (
                <form onSubmit={handleSubir} className="mt-4 border-t pt-4 space-y-3">
                    <h3 className="text-sm font-semibold text-gray-700">
                        {qr ? 'Reemplazar QR' : 'Subir QR'}
                    </h3>

                    {error && (
                        <div className="bg-red-50 text-red-700 text-xs px-3 py-2 rounded">{error}</div>
                    )}

                    <div>
                        <label className="block text-xs font-medium text-gray-600 mb-1">Imagen del QR</label>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={e => setArchivo(e.target.files?.[0] ?? null)}
                            className="text-sm"
                        />
                    </div>

                    <div>
                        <label className="block text-xs font-medium text-gray-600 mb-1">
                            Válido hasta (opcional)
                        </label>
                        <input
                            type="date"
                            value={validoHasta}
                            onChange={e => setValidoHasta(e.target.value)}
                            className="w-full border rounded px-2 py-1 text-sm"
                        />
                        <p className="text-xs text-gray-400 mt-1">
                            Déjalo vacío si no quieres que se marque como vencido.
                        </p>
                    </div>

                    <button
                        type="submit"
                        disabled={subiendo}
                        className="w-full bg-blue-600 text-white text-sm px-3 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        {subiendo ? 'Subiendo...' : 'Guardar QR'}
                    </button>
                </form>
            )}
        </div>
    );
};

export default QrPagoWidget;