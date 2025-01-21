import { Link, usePage } from "@inertiajs/react";
import React, { useState, useEffect, useRef } from "react";
import { Customer } from "../../Interfaces/Customer";
import styled from 'styled-components';
// @ts-ignore
import logo from '@/assets/logo.png';

type PageProps = {
  customer: Customer;
};

export default function Layout({ children }: { children: React.ReactElement }) {
  const props = usePage<PageProps>().props;
  const [menuOpen, setMenuOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setMenuOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);
  

  return (
    <>
      {/* Header */}
      <div className="bg-transparent  dark:bg-neutral-900/60 fixed top-0 left-0 w-full z-50 ">
        <div className="max-w-[85rem] px-4 py-4 sm:px-6 lg:px-8 mx-auto">
          <div className="grid justify-center sm:grid-cols-2 sm:items-center gap-4">
            <div className="flex items-center gap-x-3 md:gap-x-5">
              <Link href="/" className="hover:scale-110 hover:text-blue-600 transition-transform duration-300">
                <img src={logo} alt="Logo" className="w-10 h-10 md:w-14 md:h-14" />
              </Link>
              <Link
                href="/"
                className="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white hover:scale-110 hover:text-blue-600 transition-transform duration-300"
              >
                AESTHECTIC
              </Link>
            </div>

            <div className="text-center sm:text-start flex sm:justify-end sm:items-center gap-x-3 md:gap-x-4">
              {!props.customer ? (
                <>
                  <Link
                    className="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 hover:scale-105 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none transition-transform duration-300"
                    href="/customer/login"
                  >
                    Iniciar sesión
                  </Link>
                  <Link
                    className="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-800 text-gray-800 hover:border-blue-500 hover:text-blue-500 hover:scale-105 focus:outline-none focus:text-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:border-white dark:text-white dark:hover:text-blue-400 dark:focus:text-blue-400 transition-transform duration-300"
                    href="/customer/register"
                  >
                    Registrarse
                  </Link>
                </>
              ) : (
                <div className="relative" ref={menuRef}>
                  <button
                    className={`py-3 px-4 flex items-center justify-between w-40 text-sm font-medium rounded-lg border ${
                      menuOpen
                        ? "border-blue-600 text-blue-600 bg-blue-50"
                        : "border-gray-800 text-gray-800 dark:border-white dark:text-white"
                    } hover:border-blue-500 hover:text-blue-500 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-transform duration-300`}
                    onClick={() => setMenuOpen(!menuOpen)}
                  >
                    {props.customer.first_name}
                    <svg
                      className={`w-4 h-4 ml-2 transition-transform transform ${
                        menuOpen ? "rotate-180" : "rotate-0"
                      }`}
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M19 9l-7 7-7-7"
                      />
                    </svg>
                  </button>

                  {menuOpen && (
                    <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg">
                      <Link
                        href="/bookings"
                        className="block px-4 py-2 text-sm text-gray-700 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-t-lg"
                      >
                        Reservas
                      </Link>
                      <Link
                        href="/Settings"
                        className="block px-4 py-2 text-sm text-gray-700 dark:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800"
                      >
                        Configuración
                      </Link>
                      <Link
                        href="/customer/logout"
                        className="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-b-lg"
                      >
                        Cerrar sesión
                      </Link>
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <main className="pt-20">{children}</main>

{/* Footer */}
<footer className="bg-blue-600 text-white py-2 text-sm">
  <div className="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto">
    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
      {/* Columna 1 */}
      <div>
        <h3 className="text-base font-bold mb-2">Aesthectic</h3>
        <p className="text-xs text-gray-200">
          Descubre nuestros servicios de estética y bienestar. Estamos aquí para ayudarte a sentirte mejor contigo mismo.
        </p>
      </div>

      {/* Columna 2 - Redes Sociales */}
      <div className="flex justify-end space-x-4">
        {/* Botón WhatsApp */}
        <a
          data-social="whatsapp"
          aria-label="Whatsapp"
          href="https://api.whatsapp.com/send?phone=+112067101079&text=Save%20this%20to%20your%20Favorites%20-%20@wilsondesouza"
          className="relative group transition-transform duration-300 transform hover:scale-110"
        >
          <svg
            xmlSpace="preserve"
            viewBox="0 0 24 24"
            className="w-6 h-6 text-gray-200 hover:text-green-400"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
          </svg>
        </a>

        {/* Botón Facebook */}
        <a
          data-social="facebook"
          aria-label="Facebook"
          href="https://www.facebook.com/"
          className="relative group transition-transform duration-300 transform hover:scale-110"
        >
          <svg
            xmlSpace="preserve"
            viewBox="0 0 24 24"
            className="w-6 h-6 text-gray-200 hover:text-blue-400"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M23.9981 11.9991C23.9981 5.37216 18.626 0 11.9991 0C5.37216 0 0 5.37216 0 11.9991C0 17.9882 4.38789 22.9522 10.1242 23.8524V15.4676H7.07758V11.9991H10.1242V9.35553C10.1242 6.34826 11.9156 4.68714 14.6564 4.68714C15.9692 4.68714 17.3424 4.92149 17.3424 4.92149V7.87439H15.8294C14.3388 7.87439 13.8739 8.79933 13.8739 9.74824V11.9991H17.2018L16.6698 15.4676H13.8739V23.8524C19.6103 22.9522 23.9981 17.9882 23.9981 11.9991Z" />
          </svg>
        </a>

        {/* Botón Instagram */}
        <a
          data-social="instagram"
          aria-label="Instagram"
          href="https://www.instagram.com/"
          className="relative group transition-transform duration-300 transform hover:scale-110"
        >
          <svg
            xmlSpace="preserve"
            viewBox="0 0 16 16"
            className="w-6 h-6 text-gray-200 hover:text-pink-400"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.244-.046zm2.184 1.274c-.838 0-1.558.295-2.153.89-.594.595-.89 1.314-.89 2.152 0 .837.296 1.557.89 2.152.595.594 1.315.89 2.153.89s1.557-.296 2.153-.89c.594-.595.89-1.315.89-2.152s-.296-1.557-.89-2.152c-.596-.595-1.315-.89-2.153-.89z" />
          </svg>
        </a>
      </div>
    </div>
  </div>
</footer>



    </>
  );
}

