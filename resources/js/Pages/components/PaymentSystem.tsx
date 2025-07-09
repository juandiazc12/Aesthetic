import React, { useState } from 'react';
import { CreditCard, Banknote, Building2, Check, X, Loader2 } from 'lucide-react';
import axios from 'axios';

interface PaymentSystemProps {
  amount: number;
  serviceId: number;
  serviceName: string;
  bookingId: number;
  customerEmail: string;
  customerName: string;
  scheduledAt: string;
  professionalId: number;
  onPaymentComplete: (paymentDetails: PaymentDetails) => void;
  onCancel?: () => void;
}

export interface PaymentDetails {
  method: 'cash' | 'card' | 'transfer';
  status: 'pending' | 'completed' | 'failed';
  transactionId?: string;
  amount: number;
  cardType?: string;
  bankName?: string;
  preferenceId?: string;
}

interface PaymentBannerProps {
  onClose: () => void;
  onConfirm: (details: any) => void;
  type: 'card' | 'transfer' | 'cash';
  isProcessing?: boolean;
}

const PaymentBanner: React.FC<PaymentBannerProps> = ({
  onClose,
  onConfirm,
  type,
  isProcessing = false,
}) => {
  const [selected, setSelected] = useState('');

  if (type === 'card') {
    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div className="bg-white rounded-lg p-6 max-w-xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold">Pago con Tarjeta</h3>
            <button
              onClick={onClose}
              className="p-2 hover:bg-gray-100 rounded-full"
              disabled={isProcessing}
            >
              <X className="w-5 h-5" />
            </button>
          </div>
          <div className="mb-4 p-4 bg-blue-50 rounded-lg">
            <p className="text-sm text-blue-800">
              Serás redirigido a MercadoPago para completar tu pago de forma segura
            </p>
          </div>
          <div className="flex gap-3">
            <button
              onClick={onClose}
              disabled={isProcessing}
              className="flex-1 py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              onClick={() => onConfirm({ type: 'card', entity: 'mercadopago' })}
              disabled={isProcessing}
              className="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2"
            >
              {isProcessing ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  Procesando...
                </>
              ) : (
                'Continuar'
              )}
            </button>
          </div>
        </div>
      </div>
    );
  }

  const bankOptions = [
    { id: 'bancolombia', name: 'Bancolombia' },
    { id: 'davivienda', name: 'Davivienda' },
    { id: 'bbva', name: 'BBVA' },
    { id: 'bogota', name: 'Banco de Bogotá' },
    { id: 'popular', name: 'Banco Popular' },
    { id: 'occidente', name: 'Banco de Occidente' },
  ];

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-xl font-bold">Pago PSE</h3>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-full"
            disabled={isProcessing}
          >
            <X className="w-5 h-5" />
          </button>
        </div>
        <div className="mb-4 p-4 bg-green-50 rounded-lg">
          <p className="text-sm text-green-800">
            Selecciona tu banco para pagar mediante PSE (Pagos Seguros en Línea)
          </p>
        </div>
        <div className="space-y-3 mb-6">
          {bankOptions.map((option) => (
            <button
              key={option.id}
              onClick={() => setSelected(option.id)}
              disabled={isProcessing}
              className={`w-full p-4 border rounded-lg flex items-center justify-between transition-colors
                ${selected === option.id
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200 hover:border-blue-200'
                } ${isProcessing ? 'opacity-50 cursor-not-allowed' : ''}`}
            >
              <span>{option.name}</span>
              {selected === option.id && <Check className="w-5 h-5 text-blue-500" />}
            </button>
          ))}
        </div>
        <div className="flex gap-3">
          <button
            onClick={onClose}
            disabled={isProcessing}
            className="flex-1 py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            Cancelar
          </button>
          <button
            onClick={() => selected && onConfirm({ type: 'transfer', entity: selected })}
            disabled={!selected || isProcessing}
            className={`flex-1 py-2 px-4 rounded-lg text-white flex items-center justify-center gap-2
              ${selected && !isProcessing
                ? 'bg-blue-600 hover:bg-blue-700'
                : 'bg-gray-400 cursor-not-allowed'}`}
          >
            {isProcessing ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                Procesando...
              </>
            ) : (
              'Confirmar'
            )}
          </button>
        </div>
      </div>
    </div>
  );
};

export function PaymentSystem({
  amount,
  serviceId,
  serviceName,
  bookingId,
  customerEmail,
  customerName,
  scheduledAt,
  professionalId,
  onPaymentComplete,
  onCancel,
}: PaymentSystemProps) {
  const [selectedMethod, setSelectedMethod] = useState<'cash' | 'card' | 'transfer' | null>(null);
  const [showBanner, setShowBanner] = useState(false);
  const [paymentDetails, setPaymentDetails] = useState<{ type: string; entity: string } | null>(null);
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleMethodSelect = (method: 'cash' | 'card' | 'transfer') => {
    setSelectedMethod(method);
    setError(null);

    if (method === 'cash') {
      setPaymentDetails({ type: 'cash', entity: 'efectivo' });
      handleCashPayment();
    } else {
      setShowBanner(true);
    }
  };

  const handleBannerConfirm = async (details: { type: string; entity: string }) => {
    setPaymentDetails(details);
    setShowBanner(false);

    if (details.type === 'card' || details.type === 'transfer') {
      await processMercadoPagoPayment(details.type);
    }
  };

  const processMercadoPagoPayment = async (paymentType: string) => {
    setIsProcessing(true);
    setError(null);

    try {
      const tempId = `temp_${Date.now()}`;
      const paymentData = {
        service_id: serviceId,
        professional_id: professionalId,
        scheduled_at: scheduledAt,
        payment_method: paymentType === 'card' ? 'mercado_pago' : 'transfer',
        payment_transaction_id: `mp_${Date.now()}`,
        payment_amount: amount,
        temp_id: tempId,
      };

      const response = await axios.post('/api/booking/create', paymentData, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });

      if (!response.data.success || !response.data.redirect_url) {
        throw new Error(response.data.error || 'No se pudo procesar el pago');
      }

      localStorage.setItem(`booking_data_${tempId}`, JSON.stringify(response.data.booking_data));
      window.location.href = response.data.redirect_url;
    } catch (err: any) {
      let errorMessage = 'Error al procesar el pago';
      if (err.response?.data?.error) {
        errorMessage = err.response.data.error;
      } else if (err.message) {
        errorMessage = err.message;
      }
      setError(errorMessage);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleCashPayment = async () => {
    setIsProcessing(true);
    setError(null);

    try {
      const tempId = `temp_${Date.now()}`;
      const response = await axios.post('/api/booking/create', {
        service_id: serviceId,
        professional_id: professionalId,
        scheduled_at: scheduledAt,
        payment_method: 'efectivo',
        payment_transaction_id: `cash_${Date.now()}`,
        temp_id: tempId,
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });

      if (!response.data.success || !response.data.redirect_url) {
        throw new Error(response.data.error || 'No se pudo crear la reserva');
      }

      localStorage.setItem(`booking_data_${tempId}`, JSON.stringify(response.data.booking_data));
      window.location.href = response.data.redirect_url;
    } catch (error: any) {
      setError(error.response?.data?.error || error.message || 'Error al procesar el pago en efectivo');
    } finally {
      setIsProcessing(false);
    }
  };

  const handlePayment = () => {
    if (selectedMethod === 'card' || selectedMethod === 'transfer') {
      if (paymentDetails) {
        processMercadoPagoPayment(paymentDetails.type);
      }
    }
  };

  return (
    <div className="w-full max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
      {onCancel && (
        <button
          onClick={onCancel}
          className="mb-4 text-gray-600 hover:text-gray-800 flex items-center gap-2"
          disabled={isProcessing}
        >
          ← Volver
        </button>
      )}

      <div className="text-center mb-6">
        <h2 className="text-2xl font-bold text-gray-800 mb-2">Método de Pago</h2>
        <p className="text-gray-600">Selecciona cómo deseas pagar</p>
      </div>

      <div className="space-y-4 mb-6">
        <PaymentOption
          icon={<Banknote className="w-6 h-6" />}
          title="Efectivo"
          description="Pago en el establecimiento"
          selected={selectedMethod === 'cash'}
          onClick={() => handleMethodSelect('cash')}
          disabled={isProcessing}
        />
        <PaymentOption
          icon={<CreditCard className="w-6 h-6" />}
          title="Tarjeta"
          description="Débito o Crédito (MercadoPago)"
          selected={selectedMethod === 'card'}
          onClick={() => handleMethodSelect('card')}
          disabled={isProcessing}
        />
        <PaymentOption
          icon={<Building2 className="w-6 h-6" />}
          title="PSE"
          description="Transferencia bancaria segura"
          selected={selectedMethod === 'transfer'}
          onClick={() => handleMethodSelect('transfer')}
          disabled={isProcessing}
        />
      </div>

      <div className="border-t pt-4">
        <div className="flex justify-between items-center mb-4">
          <span className="text-gray-600">Total a pagar:</span>
          <span className="text-2xl font-bold text-gray-800">
            ${amount.toLocaleString('es-CO')} COP
          </span>
        </div>

        {error && (
          <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p className="text-red-800 text-sm">{error}</p>
          </div>
        )}

        <button
          onClick={handlePayment}
          disabled={!selectedMethod || isProcessing}
          className={`w-full py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2
            ${(!selectedMethod || isProcessing)
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800'}`}
        >
          {isProcessing ? (
            <>
              <Loader2 className="w-5 h-5 animate-spin" />
              Procesando pago...
            </>
          ) : (
            <>
              {selectedMethod === 'cash' ? 'Confirmar Reserva' : 'Ir a Pagar'}
            </>
          )}
        </button>
      </div>

      {showBanner && (
        <PaymentBanner
          type={selectedMethod as 'card' | 'transfer' | 'cash'}
          onClose={() => {
            setShowBanner(false);
            setSelectedMethod(null);
            setPaymentDetails(null);
          }}
          onConfirm={handleBannerConfirm}
          isProcessing={isProcessing}
        />
      )}
    </div>
  );
}

interface PaymentOptionProps {
  icon: React.ReactNode;
  title: string;
  description: string;
  selected: boolean;
  onClick: () => void;
  disabled?: boolean;
}

function PaymentOption({ icon, title, description, selected, onClick, disabled }: PaymentOptionProps) {
  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={`w-full flex items-center p-4 border-2 rounded-lg transition-all
        ${selected
          ? 'border-blue-600 bg-blue-50 shadow-md'
          : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'}
        ${disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
    >
      <div className={`p-3 rounded-full mr-4 transition-colors
        ${selected ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'}`}>
        {icon}
      </div>
      <div className="flex-1 text-left">
        <h3 className="font-semibold text-gray-800">{title}</h3>
        <p className="text-sm text-gray-600">{description}</p>
      </div>
      {selected && (
        <Check className="w-6 h-6 text-blue-600 ml-2" />
      )}
    </button>
  );
}