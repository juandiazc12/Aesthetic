import React, { useState } from 'react';
import styled from 'styled-components';
import axios from 'axios';

interface CreditCardFormProps {
  onSubmit: (cardData: {
    cardNumber: string;
    cardHolder: string;
    expiryDate: string;
    cvv: string;
    bankName?: string;
    bankLogo?: string;
  }) => void;
}

const CreditCardForm = ({ onSubmit }: CreditCardFormProps) => {
  const [isFlipped, setIsFlipped] = useState(false);
  const [cardData, setCardData] = useState({
    cardNumber: '',
    cardHolder: '',
    expiryDate: '',
    cvv: '',
  });
  const [bankInfo, setBankInfo] = useState<{
    scheme: string;
    type: string;
    logo: string;
    country: { name: string; emoji: string }
  } | null>(null);
  const [bankName, setBankName] = useState('');
  const [error, setError] = useState('');
  const [cardStyle, setCardStyle] = useState<string>('default');

  const handleCardNumberChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { value } = e.target;
    const formattedValue = value
      .replace(/\s/g, '')
      .replace(/(\d{4})/g, '$1 ')
      .trim()
      .slice(0, 19);

    setCardData(prev => ({ ...prev, cardNumber: formattedValue }));

    if (formattedValue.replace(/\s/g, '').length >= 6) {
      fetchBankInfo(formattedValue);
    }
  };

  const validateCardNumber = (number: string) => {
    const regex = /^[0-9]{13,19}$/;
    return regex.test(number);
  };

  const fetchBankInfo = async (cardNumber: string) => {
    try {
      const cleanNumber = cardNumber.replace(/\s/g, '');
      if (cleanNumber.length < 6) return;

      const bin = cleanNumber.substring(0, 6);
      
      const response = await axios.get(`/api/binlist/${bin}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      
      if (response.data.error) {
        setBankInfo(null);
        throw new Error(response.data.error);
      }

      setBankInfo({
        scheme: response.data.scheme,
        type: response.data.type,
        logo: response.data.logo,
        country: response.data.country
      });

      setCardStyle(response.data.scheme.toLowerCase());
      setError('');
    } catch (err: any) {
      console.error('Error:', err);
      setBankInfo(null);
      setError(err.response?.data?.error || err.message || 'Error al obtener información de la tarjeta');
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    let formattedValue = value;

    if (name === 'cardNumber') {
      formattedValue = value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim().slice(0, 19);
    }

    if (name === 'expiryDate') {
      formattedValue = value.replace(/\D/g, '').replace(/(\d{2})(\d{0,2})/, '$1/$2').slice(0, 5);
    }

    if (name === 'cvv') {
      formattedValue = value.replace(/\D/g, '').slice(0, 3);
    }

    setCardData((prev) => ({ ...prev, [name]: formattedValue }));

    if (name === 'cardNumber' && formattedValue.replace(/\s/g, '').length >= 6) {
      fetchBankInfo(formattedValue);
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    fetchBankInfo(cardData.cardNumber);
  };

  return (
    <StyledWrapper>
      <form onSubmit={handleSubmit} className="card-form">
        <div className={`flip-card ${isFlipped ? 'flipped' : ''}`}>
          <div className="flip-card-inner">
            <div className="flip-card-front">
              <div className="chip" />
              {bankInfo?.logo && (
                <div className="card-logo">
                  <img src={bankInfo.logo} alt={bankInfo.scheme} />
                </div>
              )}
              <p className="number">{cardData.cardNumber || '•••• •••• •••• ••••'}</p>
              <span className="card-scheme">{bankInfo?.scheme || 'CARD'}</span>
              <p className="valid_thru">{cardData.expiryDate || 'MM/YY'}</p>
              <p className="name">{cardData.cardHolder || 'CARD HOLDER'}</p>
            </div>

            <div className="flip-card-back">
              <div className="strip" />
              <p className="cvv">{cardData.cvv || '•••'}</p>
            </div>
          </div>
        </div>

        <div className="input-group">
          <label>Número de Tarjeta</label>
          <input
            type="text"
            name="cardNumber"
            value={cardData.cardNumber}
            onChange={handleCardNumberChange}
            placeholder="1234 5678 9012 3456"
          />
        </div>

        <div className="input-group">
          <label>Titular de la Tarjeta</label>
          <input
            type="text"
            name="cardHolder"
            value={cardData.cardHolder}
            onChange={handleInputChange}
            placeholder="Nombre del titular"
          />
        </div>

        <div className="input-row">
          <div className="input-group">
            <label>Fecha de Vencimiento</label>
            <input
              type="text"
              name="expiryDate"
              value={cardData.expiryDate}
              onChange={handleInputChange}
              placeholder="MM/YY"
            />
          </div>

          <div className="input-group">
            <label>CVV</label>
            <input
              type="text"
              name="cvv"
              value={cardData.cvv}
              onChange={handleInputChange}
              onFocus={() => setIsFlipped(true)}
              onBlur={() => setIsFlipped(false)}
              placeholder="123"
            />
          </div>
        </div>

        {error && <p style={{ color: 'red' }}>{error}</p>}
        {bankName && <p>Banco: {bankName}</p>}

        <button type="submit">Confirmar Tarjeta</button>
      </form>
    </StyledWrapper>
  );
};

const StyledWrapper = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;

  .card-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    width: 280px;
    transition: transform 0.3s;
    &:hover {
      transform: scale(1.02);
    }
  }

  .flip-card {
    background-color: transparent;
    width: 240px;
    height: 154px;
    perspective: 1000px;
    color: white;
  }

  .flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s;
    transform-style: preserve-3d;
  }

  .flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
  }

  .flip-card-front, .flip-card-back {
    box-shadow: 0 8px 14px rgba(0, 0, 0, 0.2);
    position: absolute;
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 1rem;
    background-color: #171717;
  }

  .flip-card-back {
    transform: rotateY(180deg);
  }

  .chip {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 40px;
    height: 25px;
    background: linear-gradient(135deg, #d1d1d1, #a1a1a1);
    border-radius: 5px;
    box-shadow: inset 0 1px 3px rgba(255, 255, 255, 0.5), 
                inset 0 -1px 2px rgba(0, 0, 0, 0.2);
    border: 1px solid #999;
  }

  .number {
    position: absolute;
    font-weight: bold;
    font-size: 1.2em;
    top: 60px;
    left: 20px;
  }

  .bank-name {
    position: absolute;
    font-weight: bold;
    font-size: 0.8em;
    top: 90px;
    left: 20px;
  }

  .name {
    position: absolute;
    font-weight: bold;
    font-size: 0.8em;
    top: 120px;
    left: 20px;
  }

  .valid_thru {
    position: absolute;
    font-weight: bold;
    font-size: 0.6em;
    top: 140px;
    left: 20px;
  }

  .input-group {
    margin-bottom: 10px;
  }

  input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: border 0.3s;
    font-size: 14px;
    &:focus {
      border-color: #007bff;
      outline: none;
    }
  }

  button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;

    &:hover {
      background-color: #0056b3;
    }
  }

  .bank-info {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;

    .bank-logo {
      width: 40px;
      height: 40px;
      margin-left: 5px;
    }

    .bank-name {
      font-weight: bold;
      color: #333;
      margin-right: 5px;
    }
  }

  .card-logo {
    position: absolute;
    top: 15px;
    right: 15px;
    height: 40px;
    
    img {
      height: 100%;
      width: auto;
      object-fit: contain;
    }
  }

  .card-scheme {
    position: absolute;
    font-weight: bold;
    font-size: 0.8em;
    top: 90px;
    left: 20px;
    text-transform: uppercase;
  }
`;

export default CreditCardForm;
