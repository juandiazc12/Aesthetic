import {Daum} from "@/Interfaces/Service";
import {Customer} from "@/Interfaces/Customer";

export interface Booking {
  id: number
  customer_id: number
  service_id: number
  scheduled_at: string
  professional_id: number
  status: string
  notes: any
  created_at: string
  updated_at: string
  service: Daum
  customer: Customer
  professional: Professional
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
