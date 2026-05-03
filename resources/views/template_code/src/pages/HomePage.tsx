import React, { useState } from 'react';
import { Search, MapPin, Star, ChevronRight, Coffee, Pizza, IceCream, Beer, Utensils } from 'lucide-react';

export default function HomePage({ navigateTo, places }: { navigateTo: (page: string, data?: any) => void, places: any[] }) {
  const [searchQuery, setSearchQuery] = useState('');

  const categories = [
    { name: 'Restaurants', icon: <Utensils size={24} /> },
    { name: 'Street Food', icon: <Pizza size={24} /> },
    { name: 'Cafés', icon: <Coffee size={24} /> },
    { name: 'Desserts', icon: <IceCream size={24} /> },
    { name: 'Drinks', icon: <Beer size={24} /> },
  ];

  return (
    <div className="bg-gray-50 min-h-screen pb-12">
      {/* Hero Section */}
      <div className="relative bg-charcoal text-white py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div className="absolute inset-0 z-0">
          <img 
            src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1920&q=80" 
            alt="Food background" 
            className="w-full h-full object-cover opacity-30"
          />
        </div>
        <div className="relative z-10 max-w-3xl mx-auto text-center">
          <h1 className="text-4xl sm:text-5xl font-bold tracking-tight mb-6">
            Discover your next <span className="text-primary">favorite bite</span>
          </h1>
          <p className="text-lg sm:text-xl text-gray-300 mb-8">
            Find the best restaurants, cafés, and hidden street food gems in your city.
          </p>
          
          {/* Search Bar */}
          <div className="flex items-center bg-white rounded-full p-2 shadow-xl max-w-2xl mx-auto">
            <div className="flex-grow flex items-center pl-4">
              <Search className="text-gray-400" size={20} />
              <input 
                type="text" 
                placeholder="Find food near you..." 
                className="w-full py-3 px-4 text-gray-800 focus:outline-none bg-transparent"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
            </div>
            <button 
              onClick={() => navigateTo('explore')}
              className="bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-full font-medium transition-colors"
            >
              Search
            </button>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        {/* Categories */}
        <div className="mb-12">
          <h2 className="text-2xl font-bold text-charcoal mb-6">What are you craving?</h2>
          <div className="flex overflow-x-auto pb-4 space-x-4 hide-scrollbar">
            {categories.map((category, index) => (
              <button 
                key={index}
                className="flex flex-col items-center justify-center min-w-[100px] p-4 bg-white rounded-2xl shadow-sm hover:shadow-md hover:border-primary border border-transparent transition-all group"
              >
                <div className="w-14 h-14 rounded-full bg-cream text-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                  {category.icon}
                </div>
                <span className="text-sm font-medium text-gray-700">{category.name}</span>
              </button>
            ))}
          </div>
        </div>

        {/* Featured Section */}
        <div>
          <div className="flex justify-between items-end mb-6">
            <h2 className="text-2xl font-bold text-charcoal">Trending Spots</h2>
            <button 
              onClick={() => navigateTo('explore')}
              className="text-primary font-medium flex items-center hover:underline"
            >
              See all <ChevronRight size={16} />
            </button>
          </div>
          
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {places.map((place) => (
              <div 
                key={place.id} 
                onClick={() => navigateTo('detail', place)}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow cursor-pointer border border-gray-100 group"
              >
                <div className="relative h-48 overflow-hidden">
                  <img 
                    src={place.image} 
                    alt={place.name} 
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                  <div className="absolute top-3 right-3 bg-white px-2 py-1 rounded-lg text-sm font-bold shadow-md flex items-center">
                    <Star size={14} className="text-yellow-400 fill-yellow-400 mr-1" />
                    {place.rating}
                  </div>
                </div>
                <div className="p-5">
                  <div className="flex justify-between items-start mb-2">
                    <h3 className="text-xl font-bold text-charcoal truncate">{place.name}</h3>
                    <span className="text-gray-500 font-medium">{place.priceRange}</span>
                  </div>
                  <p className="text-gray-500 text-sm mb-4 line-clamp-2">{place.description}</p>
                  <div className="flex items-center justify-between text-sm text-gray-500">
                    <div className="flex items-center">
                      <MapPin size={16} className="mr-1 text-primary" />
                      <span className="truncate max-w-[120px]">{place.location}</span>
                    </div>
                    <span>{place.distance}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
