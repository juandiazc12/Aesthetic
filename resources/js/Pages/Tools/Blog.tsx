import Layout from "@/Layouts/Layout";
import { Head } from "@inertiajs/react";

export default function Blog() {
  const blogPosts = [
    {
      id: 1,
      title: "Tendencias en Estética 2025",
      excerpt: "Descubre las últimas tendencias en tratamientos estéticos que están revolucionando la industria de la belleza.",
      date: "2025-01-15",
      author: "Dr. María González",
      image: "https://via.placeholder.com/400x250",
      category: "Tendencias"
    },
    {
      id: 2,
      title: "Cuidados Post-Tratamiento",
      excerpt: "Guía completa sobre los cuidados necesarios después de realizarte un tratamiento estético.",
      date: "2025-01-10",
      author: "Dra. Ana Rodríguez",
      image: "https://via.placeholder.com/400x250",
      category: "Cuidados"
    },
    {
      id: 3,
      title: "Mitos y Realidades de la Estética",
      excerpt: "Desmitificamos las creencias más comunes sobre los tratamientos de belleza y estética.",
      date: "2025-01-05",
      author: "Dr. Carlos Martínez",
      image: "https://via.placeholder.com/400x250",
      category: "Educación"
    }
  ];

  return (
    
      <>
        <Head title="Blog - AESTHETIC" />
        
        <div className="max-w-6xl mx-auto py-8">
          {/* Header */}
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
              Blog AESTHETIC
            </h1>
            <p className="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
              Mantente al día con las últimas tendencias, consejos y novedades del mundo de la estética
            </p>
          </div>

          {/* Featured Post */}
          {blogPosts.length > 0 && (
            <div className="mb-12">
              <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg overflow-hidden">
                <div className="md:flex">
                  <div className="md:w-1/2">
                    <img 
                      src={blogPosts[0].image} 
                      alt={blogPosts[0].title}
                      className="w-full h-64 md:h-full object-cover"
                    />
                  </div>
                  <div className="md:w-1/2 p-8">
                    <span className="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mb-4">
                      Destacado
                    </span>
                    <h2 className="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                      {blogPosts[0].title}
                    </h2>
                    <p className="text-gray-600 dark:text-gray-300 mb-6">
                      {blogPosts[0].excerpt}
                    </p>
                    <div className="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                      <span>{blogPosts[0].author}</span>
                      <span className="mx-2">•</span>
                      <span>{new Date(blogPosts[0].date).toLocaleDateString('es-ES')}</span>
                    </div>
                    <button className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                      Leer más
                    </button>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Blog Grid */}
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {blogPosts.slice(1).map((post) => (
              <article key={post.id} className="bg-white dark:bg-neutral-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img 
                  src={post.image} 
                  alt={post.title}
                  className="w-full h-48 object-cover"
                />
                <div className="p-6">
                  <span className="inline-block bg-gray-100 dark:bg-neutral-700 text-gray-800 dark:text-gray-200 text-sm px-3 py-1 rounded-full mb-3">
                    {post.category}
                  </span>
                  <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-3">
                    {post.title}
                  </h3>
                  <p className="text-gray-600 dark:text-gray-300 mb-4">
                    {post.excerpt}
                  </p>
                  <div className="flex items-center justify-between">
                    <div className="text-sm text-gray-500 dark:text-gray-400">
                      <div>{post.author}</div>
                      <div>{new Date(post.date).toLocaleDateString('es-ES')}</div>
                    </div>
                    <button className="text-blue-600 hover:text-blue-700 font-medium">
                      Leer más
                    </button>
                  </div>
                </div>
              </article>
            ))}
          </div>

          {/* Newsletter Subscription */}
          <div className="mt-16 bg-blue-600 rounded-xl p-8 text-center">
            <h3 className="text-2xl font-bold text-white mb-4">
              Suscríbete a nuestro newsletter
            </h3>
            <p className="text-blue-100 mb-6">
              Recibe las últimas novedades y consejos directamente en tu correo
            </p>
            <div className="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
              <input 
                type="email" 
                placeholder="Tu correo electrónico"
                className="flex-1 px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-300"
              />
              <button className="bg-white text-blue-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Suscribirse
              </button>
            </div>
          </div>
        </div>
      </>

  );
}