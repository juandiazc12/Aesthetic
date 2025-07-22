import {Daum} from "@/Interfaces/Service";
import {Customer} from "@/Interfaces/Customer";

export interface Booking {
  id: number;
  service: {
    id: number;
    name: string;
    price: number;
    duration: number;
    image?: string;
  };
  professional: {
    id: number;
    name: string;
    email: string;
    photo?: string;
  };
  scheduled_at: string;
  scheduled_date?: string;
  scheduled_time?: string;
  scheduled_day?: string;
  total_amount: number;
  payment_method: string;
  payment_status: string;
  status: string;
  status_spanish: string;
  created_at: string;
  completed_at?: string;
}

export interface Professional {
  id: number
  photo: string
  name: string
  email: string
  email_verified_at: any
  created_at: string
  updated_at: string
}
