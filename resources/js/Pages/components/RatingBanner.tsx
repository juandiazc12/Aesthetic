// src/Pages/components/RatingBanner.tsx
import React, { useState } from "react";
import axios from "axios";

export default function RatingBanner({ booking, onClose }: { booking: any; onClose: () => void }) {
    const [rating, setRating] = useState(0);
    const [hoveredRating, setHoveredRating] = useState(0);
    const [comment, setComment] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
  e.preventDefault();


  if (rating === 0) {
    alert("Por favor selecciona una calificación");
    return;
  }

  setIsSubmitting(true);

  try {
            await axios.post("/ratings", {
                booking_id: booking.id,
                rating,
                comment,
            });

            console.log("Rating enviado exitosamente");
            onClose();
        } catch (error: any) {
            console.error("Error al enviar calificación:", error);
            const message = error.response?.data?.error || "Error al enviar la calificación. Inténtalo de nuevo.";
            alert(message);
        } finally {
            setIsSubmitting(false);
        }
};


    const handleStarClick = (star: number) => setRating(star);
    const handleStarHover = (star: number) => setHoveredRating(star);
    const handleStarLeave = () => setHoveredRating(0);

    const getStarColor = (star: number) => {
        const active = hoveredRating || rating;
        return star <= active ? "text-yellow-400" : "text-gray-300";
    };

    const getRatingText = () => {
        switch (rating) {
            case 1: return { text: "Muy malo", color: "text-red-500" };
            case 2: return { text: "Malo", color: "text-orange-500" };
            case 3: return { text: "Regular", color: "text-yellow-500" };
            case 4: return { text: "Bueno", color: "text-green-500" };
            case 5: return { text: "Excelente", color: "text-green-600" };
            default: return { text: "Selecciona una calificación", color: "text-gray-500" };
        }
    };

    const ratingInfo = getRatingText();

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-in fade-in duration-300 p-4">
            <div className="w-full max-w-5xl bg-white rounded-2xl shadow-2xl animate-in slide-in-from-bottom duration-300 max-h-[90vh] overflow-hidden">
                {/* Header con botón de cerrar */}
                <div className="flex justify-between items-center p-6 border-b border-gray-100">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h3 className="text-2xl font-bold text-gray-800">Califica tu experiencia</h3>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
                        disabled={isSubmitting}
                    >
                        <svg className="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 max-h-[80vh] overflow-y-auto">
                    {/* Información del booking con diseño mejorado */}
                    <div className="mb-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl border border-blue-100">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {/* Servicio */}
                            <div className="flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm">
                                <div className="relative">
                                    <img
                                        src={booking.service?.image || "/images/default-service.png"}
                                        alt="Servicio"
                                        className="w-20 h-20 object-cover rounded-xl border-2 border-blue-100 shadow-sm"
                                    />
                                    <div className="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg className="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                                        </svg>
                                    </div>
                                </div>
                                <div className="flex-1">
                                    <h4 className="font-bold text-gray-800 text-lg mb-1">Servicio</h4>
                                    <p className="text-gray-600 mb-2">{booking.service?.name ?? "No disponible"}</p>
                                    <div className="space-y-1 text-sm text-gray-500">
                                        <p><span className="font-medium">Fecha:</span> {booking.scheduled_at}</p>
                                        <p><span className="font-medium">ID:</span> #{booking.id}</p>
                                    </div>
                                </div>
                            </div>

                            {/* Profesional */}
                            <div className="flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm">
                                <div className="relative">
                                    <img
                                        src={booking.professional?.photo || "/images/default-user.png"}
                                        alt="Profesional"
                                        className="w-20 h-20 object-cover rounded-full border-2 border-purple-100 shadow-sm"
                                    />
                                    <div className="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div className="flex-1">
                                    <h4 className="font-bold text-gray-800 text-lg mb-1">Profesional</h4>
                                    <p className="text-gray-600 mb-2">{booking.professional?.name ?? "No disponible"}</p>
                                    <div className="flex items-center gap-1">
                                        <svg className="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span className="text-sm text-gray-500">Profesional verificado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-8">
                        {/* Sistema de calificación mejorado */}
                        <div className="text-center">
                            <label className="block text-xl font-bold text-gray-800 mb-6">
                                ¿Cómo fue tu experiencia?
                            </label>

                            <div className="flex items-center justify-center gap-3 mb-4 p-6 bg-gray-50 rounded-2xl">
                                {[1, 2, 3, 4, 5].map((star) => (
                                    <button
                                        key={star}
                                        type="button"
                                        onClick={() => handleStarClick(star)}
                                        onMouseEnter={() => handleStarHover(star)}
                                        onMouseLeave={handleStarLeave}
                                        className={`text-5xl transition-all duration-200 hover:scale-125 transform focus:outline-none ${getStarColor(star)}`}
                                        style={{ filter: star <= (hoveredRating || rating) ? 'drop-shadow(2px 2px 4px rgba(0,0,0,0.3))' : 'none' }}
                                    >
                                        ★
                                    </button>
                                ))}
                            </div>

                            <div className="flex items-center justify-center gap-2">
                                <div className={`px-4 py-2 rounded-full text-sm font-medium ${rating > 0 ? 'bg-white shadow-sm' : ''}`}>
                                    <span className={ratingInfo.color}>{ratingInfo.text}</span>
                                </div>
                                {rating > 0 && (
                                    <div className="flex items-center gap-1">
                                        {Array.from({ length: rating }, (_, i) => (
                                            <div key={i} className="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Comentario mejorado */}
                        <div>
                            <label className="block text-lg font-semibold text-gray-800 mb-3">
                                Cuéntanos más sobre tu experiencia
                            </label>
                            <div className="relative">
                                <textarea
                                    placeholder="Tu opinión es muy importante para nosotros y ayuda a otros usuarios a tomar mejores decisiones..."
                                    className="w-full border-2 border-gray-200 p-4 rounded-xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                                    rows={4}
                                    value={comment}
                                    onChange={(e) => setComment(e.target.value)}
                                    maxLength={500}
                                />
                                <div className="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-2 py-1 rounded-full shadow-sm">
                                    {comment.length}/500
                                </div>
                            </div>
                        </div>

                        {/* Botones mejorados */}
                        <div className="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                onClick={onClose}
                                className="flex-1 px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors border-2 border-gray-200 hover:border-gray-300 rounded-xl"
                                disabled={isSubmitting}
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                className="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                disabled={rating === 0 || isSubmitting}
                            >
                                {isSubmitting ? (
                                    <div className="flex items-center justify-center gap-2">
                                        <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                        Enviando...
                                    </div>
                                ) : (
                                    <div className="flex items-center justify-center gap-2">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Enviar Calificación
                                    </div>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}