import { usePage } from "@inertiajs/react";
import { useState, useMemo } from "react";
import ServicesList from "./Services/ServicesList";
import { Services } from "../../Interfaces/Service";
import { MagnifyingGlassIcon, XMarkIcon, ChevronLeftIcon, ChevronRightIcon } from "@heroicons/react/24/solid";

type PageProps = {
  services: Services;
};

export default function Welcome() {
  const props = usePage<PageProps>().props;
  const [searchTerm, setSearchTerm] = useState<string>("");
  const [currentPage, setCurrentPage] = useState<number>(1);
  const servicesPerPage = 12;

  // Filtrar servicios basado en el término de búsqueda
  const filteredServices = useMemo(() => {
    if (!searchTerm.trim()) {
      return props.services.data; // Retorna todos los servicios si no hay búsqueda
    }

    return props.services.data.filter((service) =>
      service.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      service.description.toLowerCase().includes(searchTerm.toLowerCase())
    );
  }, [props.services, searchTerm]);

  // Calcular servicios para la página actual
  const paginatedServices = useMemo(() => {
    const startIndex = (currentPage - 1) * servicesPerPage;
    const endIndex = startIndex + servicesPerPage;
    return filteredServices.slice(startIndex, endIndex);
  }, [filteredServices, currentPage, servicesPerPage]);

  // Calcular información de paginación
  const totalPages = Math.ceil(filteredServices.length / servicesPerPage);
  const totalServices = filteredServices.length;

  // Resetear página cuando cambie el filtro
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(e.target.value);
    setCurrentPage(1); // Resetear a la primera página cuando se filtre
  };

  const clearSearch = () => {
    setSearchTerm("");
    setCurrentPage(1);
  };

  const handlePageChange = (page: number) => {
    setCurrentPage(page);
    // Scroll suave hacia arriba
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // Generar números de página para mostrar
  const getPageNumbers = () => {
    const pages = [];
    const maxVisiblePages = 5;
    
    if (totalPages <= maxVisiblePages) {
      // Si hay pocas páginas, mostrar todas
      for (let i = 1; i <= totalPages; i++) {
        pages.push(i);
      }
    } else {
      // Lógica para mostrar páginas alrededor de la actual
      const startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
      const endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
      
      if (startPage > 1) {
        pages.push(1);
        if (startPage > 2) pages.push('...');
      }
      
      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      
      if (endPage < totalPages) {
        if (endPage < totalPages - 1) pages.push('...');
        pages.push(totalPages);
      }
    }
    
    return pages;
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section con búsqueda */}
      <div className="relative mb-8">
        <div className="relative h-80 overflow-hidden">
          <img
            src="https://i.pinimg.com/1200x/b6/19/2b/b6192b03134641e47fa92f7d8aba0e98.jpg"
            className="w-full h-full object-cover"
            alt="Hero Background"
          />
          <div className="absolute inset-0 bg-black bg-opacity-40"></div>
        </div>
        
        {/* Search Bar */}
        <div className="absolute inset-0 flex items-center justify-center px-4">
          <div className="w-full max-w-3xl">
            <h1 className="text-4xl md:text-5xl font-bold text-white text-center mb-6">
              Encuentra tu servicio ideal
            </h1>
            <div className="bg-white rounded-2xl shadow-2xl p-2">
              <form onSubmit={handleSearch} className="flex items-center">
                <div className="relative flex-1">
                  <MagnifyingGlassIcon className="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    type="text"
                    value={searchTerm}
                    onChange={handleInputChange}
                    placeholder="Buscar servicio o tratamiento..."
                    className="w-full pl-12 pr-12 py-4 text-lg border-0 focus:ring-0 focus:outline-none rounded-l-2xl"
                  />
                  {searchTerm && (
                    <button
                      type="button"
                      onClick={clearSearch}
                      className="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                    >
                      <XMarkIcon className="w-5 h-5" />
                    </button>
                  )}
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      {/* Content Section */}
      <div className="container mx-auto px-4 pb-12">

        {/* No Results Message */}
        {searchTerm && filteredServices.length === 0 && (
          <div className="text-center py-16 bg-white rounded-xl shadow-sm">
            <div className="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <MagnifyingGlassIcon className="w-12 h-12 text-gray-400" />
            </div>
            <h3 className="text-2xl font-bold text-gray-800 mb-3">
              No se encontraron resultados
            </h3>
            <p className="text-gray-500 mb-6 text-lg">
              No hay servicios que coincidan con "{searchTerm}"
            </p>
            <button
              onClick={clearSearch}
              className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center gap-2"
            >
              <XMarkIcon className="w-5 h-5" />
              Ver todos los servicios
            </button>
          </div>
        )}

        {/* Services List */}
        {paginatedServices.length > 0 && (
          <>
            <ServicesList services={{ data: paginatedServices }} />
            
            {/* Pagination */}
            {totalPages > 1 && (
              <div className="mt-12 flex flex-col items-center space-y-4">
                {/* Page Info */}
                <div className="text-sm text-gray-500">
                  Mostrando {((currentPage - 1) * servicesPerPage) + 1} - {Math.min(currentPage * servicesPerPage, totalServices)} de {totalServices} servicios
                </div>
                
                {/* Pagination Controls */}
                <div className="flex items-center space-x-2">
                  {/* Previous Button */}
                  <button
                    onClick={() => handlePageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                    className={`flex items-center px-3 py-2 rounded-lg font-medium transition-colors ${
                      currentPage === 1
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border'
                    }`}
                  >
                    <ChevronLeftIcon className="w-4 h-4 mr-1" />
                    Anterior
                  </button>

                  {/* Page Numbers */}
                  <div className="flex space-x-1">
                    {getPageNumbers().map((page, index) => (
                      <button
                        key={index}
                        onClick={() => typeof page === 'number' && handlePageChange(page)}
                        disabled={page === '...'}
                        className={`px-3 py-2 rounded-lg font-medium transition-colors ${
                          page === currentPage
                            ? 'bg-blue-600 text-white shadow-sm'
                            : page === '...'
                            ? 'text-gray-400 cursor-default'
                            : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border'
                        }`}
                      >
                        {page}
                      </button>
                    ))}
                  </div>

                  {/* Next Button */}
                  <button
                    onClick={() => handlePageChange(currentPage + 1)}
                    disabled={currentPage === totalPages}
                    className={`flex items-center px-3 py-2 rounded-lg font-medium transition-colors ${
                      currentPage === totalPages
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border'
                    }`}
                  >
                    Siguiente
                    <ChevronRightIcon className="w-4 h-4 ml-1" />
                  </button>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}