import React, { useState } from 'react';
import { CreditCard, Banknote, Building2, Check, X } from 'lucide-react';
import CreditCardForm from './CreditCardForm';

interface PaymentSystemProps {
  amount: number;
  onPaymentComplete: (paymentDetails: PaymentDetails) => void;
  onCancel?: () => void;
}

export interface PaymentDetails {
  method: 'cash' | 'card' | 'transfer';
  status: 'pending' | 'completed';
  transactionId: string;
  amount: number;
  cardType?: string;
  bankName?: string;
}

interface PaymentBannerProps {
  onClose: () => void;
  onConfirm: (details: { type: string, entity: string }) => void;
  type: 'card' | 'transfer' | 'cash';
}

const PaymentBanner: React.FC<PaymentBannerProps> = ({ onClose, onConfirm, type }) => {
  const [selected, setSelected] = useState('');

  if (type === 'card') {
    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div className="bg-white rounded-lg p-6 max-w-xl w-full mx-4">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold">Ingresa los datos de tu tarjeta</h3>
            <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full">
              <X className="w-5 h-5" />
            </button>
          </div>
          
          <CreditCardForm
            onSubmit={(cardData) => {
              onConfirm({
                type: 'card',
                entity: 'credit',
                ...cardData
              });
            }}
          />
        </div>
      </div>
    );
  }

  const bankOptions = [
    { id: 'bancolombia', name: 'Bancolombia' },
    { id: 'davivienda', name: 'Davivienda' },
    { id: 'bbva', name: 'BBVA' },
    { id: 'bogota', name: 'Banco de Bogotá' },
  ];

  const options = bankOptions;
  const title = type === 'transfer' ? 'Selecciona tu banco (PSE)' : 'Selecciona tu tarjeta';

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-xl font-bold">{title}</h3>
          <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full">
            <X className="w-5 h-5" />
          </button>
        </div>
        
        <div className="space-y-3 mb-6">
          {options.map((option) => (
            <button
              key={option.id}
              onClick={() => setSelected(option.id)}
              className={`w-full p-4 border rounded-lg flex items-center justify-between
                ${selected === option.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-200'}`}
            >
              <span>{option.name}</span>
              {selected === option.id && <Check className="w-5 h-5 text-blue-500" />}
            </button>
          ))}
        </div>

        <div className="flex gap-3">
          <button
            onClick={onClose}
            className="flex-1 py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            onClick={() => selected && onConfirm({ type: type, entity: selected })}
            disabled={!selected}
            className={`flex-1 py-2 px-4 rounded-lg text-white
              ${selected ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400'}`}
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  );
};

export function PaymentSystem({ amount, onPaymentComplete, onCancel }: PaymentSystemProps) {
  const [selectedMethod, setSelectedMethod] = useState<'cash' | 'card' | 'transfer' | null>(null);
  const [showBanner, setShowBanner] = useState(false);
  const [paymentDetails, setPaymentDetails] = useState<{ type: string, entity: string } | null>(null);
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleMethodSelect = (method: 'cash' | 'card' | 'transfer') => {
    setSelectedMethod(method);
    if (method === 'card' || method === 'transfer') {
      setShowBanner(true);
    }
  };

  const handleBannerConfirm = (details: { type: string, entity: string }) => {
    setPaymentDetails(details);
    setShowBanner(false);
  };

  const handlePayment = async () => {setIsProcessing(true);
  setError(null);

  try {
    if (selectedMethod === 'cash') {
      const details: PaymentDetails = {
        method: 'cash',
        status: 'completed',
        transactionId: Math.random().toString(36).substring(2, 15),
        amount: amount
      };
      onPaymentComplete(details);
    } else {
      // Llama al backend para obtener el link de pago
      const response = await fetch('http://localhost:8000/api/create-preference', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ amount })
      });

      const data = await response.json();
      window.location.href = data.init_point; // Redirige al Checkout Pro de Mercado Pago
    }
  } catch (err: any) {
    setError("Error al procesar el pago.");
  } finally {
    setIsProcessing(false);
  }
};

  return (
    <div className="w-full max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
      {onCancel && (
        <button onClick={onCancel} className="mb-4 text-gray-600 hover:text-gray-800">
          ← Volver
        </button>
      )}
      
      <h2 className="text-2xl font-bold text-gray-800 mb-6">Método de Pago</h2>
      <div className="space-y-4">
        <div className="grid grid-cols-1 gap-4">
          <PaymentOption
            icon={<Banknote className="w-6 h-6" />}
            title="Efectivo"
            description="Pago en efectivo"
            selected={selectedMethod === 'cash'}
            onClick={() => handleMethodSelect('cash')}
          />
          
          <PaymentOption
            icon={<CreditCard className="w-6 h-6" />}
            title="Tarjeta"
            description={paymentDetails?.type === 'card' ? `Pago con ${paymentDetails.entity}` : "Débito o Crédito"}
            selected={selectedMethod === 'card'}
            onClick={() => handleMethodSelect('card')}
          />
          
          <PaymentOption
            icon={<Building2 className="w-6 h-6" />}
            title="PSE"
            description={paymentDetails?.type === 'transfer' ? `Banco ${paymentDetails.entity}` : "Transferencia bancaria"}
            selected={selectedMethod === 'transfer'}
            onClick={() => handleMethodSelect('transfer')}
          />
        </div>

        <div className="mt-6">
          <div className="text-lg font-semibold text-gray-800 mb-4">
            Total a pagar: ${amount.toFixed(2)}
          </div>
          
          <button
            onClick={handlePayment}
            disabled={!selectedMethod || (selectedMethod !== 'cash' && !paymentDetails) || isProcessing}
            className={`w-full py-3 px-4 rounded-lg text-white font-medium transition-colors
              ${(!selectedMethod || (selectedMethod !== 'cash' && !paymentDetails) || isProcessing)
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-blue-600 hover:bg-blue-700'}`}
          >
            {isProcessing ? (
              <span className="flex items-center justify-center">
                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Procesando...
              </span>
            ) : (
              'Confirmar Pago'
            )}
          </button>
        </div>

        {error && <div className="text-red-500">{error}</div>}
      </div>

      {showBanner && (
        <PaymentBanner
          type={selectedMethod as 'card' | 'transfer' | 'cash'}
          onClose={() => {
            setShowBanner(false);
            setSelectedMethod(null);
          }}
          onConfirm={handleBannerConfirm}
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
}

function PaymentOption({ icon, title, description, selected, onClick }: PaymentOptionProps) {
  return (
    <button
      onClick={onClick}
      className={`flex items-center p-4 border-2 rounded-lg transition-all
        ${selected 
          ? 'border-blue-600 bg-blue-50' 
          : 'border-gray-200 hover:border-blue-400'}`}
    >
      <div className={`p-2 rounded-full mr-4 
        ${selected ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'}`}>
        {icon}
      </div>
      <div className="text-left">
        <h3 className="font-semibold text-gray-800">{title}</h3>
        <p className="text-sm text-gray-600">{description}</p>
      </div>
      {selected && (
        <Check className="w-6 h-6 text-blue-600 ml-auto" />
      )}
    </button>
  );
}