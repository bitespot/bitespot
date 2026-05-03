import React, { useState } from 'react';
import { Search, Filter, MapPin, Star, Map as MapIcon, List, Utensils, Pizza, Coffee, Plus } from 'lucide-react';

export default function ExplorePage({ navigateTo, places }: { navigateTo: (page: string, data?: any) => void, places: any[] }) {
  const [viewMode, setViewMode] = useState('map'); // 'map' or 'list'
  const [activeCategory, setActiveCategory] = useState('All');

  const categories = ['All', 'Restaurant', 'Street Food', 'Café', 'Desserts'];

  return (
    <div className="flex flex-col h-[calc(100vh-64px)] bg-gray-50 overflow-hidden">
      {/* Top Bar */}
      <div className="bg-white border-b border-gray-200 px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-4 z-10 shadow-sm">
        <div className="flex items-center w-full sm:w-auto flex-1 max-w-md bg-gray-100 rounded-full px-4 py-2">
          <Search size={18} className="text-gray-500 mr-2" />
          <input 
            type="text" 
            placeholder="Search by name or cuisine..." 
            className="bg-transparent border-none focus:outline-none w-full text-sm"
          />
        </div>
        
        <div className="flex items-center gap-3 w-full sm:w-auto overflow-x-auto hide-scrollbar pb-1 sm:pb-0">
          <button className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium hover:bg-gray-50 whitespace-nowrap shadow-sm">
            <Filter size={16} /> Filters
          </button>
          
          <div className="flex bg-gray-100 p-1 rounded-full">
            <button 
              onClick={() => setViewMode('map')}
              className={`flex items-center justify-center px-4 py-1.5 rounded-full text-sm font-medium transition-colors ${
                viewMode === 'map' ? 'bg-white shadow-sm text-primary' : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              <MapIcon size={16} className="mr-1.5" /> Map
            </button>
            <button 
              onClick={() => setViewMode('list')}
              className={`flex items-center justify-center px-4 py-1.5 rounded-full text-sm font-medium transition-colors ${
                viewMode === 'list' ? 'bg-white shadow-sm text-primary' : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              <List size={16} className="mr-1.5" /> List
            </button>
          </div>
        </div>
      </div>

      {/* Categories Filter */}
      <div className="bg-white border-b border-gray-200 px-4 py-2 overflow-x-auto hide-scrollbar z-10">
        <div className="flex gap-2 min-w-max">
          {categories.map(cat => (
            <button
              key={cat}
              onClick={() => setActiveCategory(cat)}
              className={`px-4 py-1.5 rounded-full text-sm font-medium transition-colors ${
                activeCategory === cat 
                  ? 'bg-charcoal text-white' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </div>

      {/* Main Content Area */}
      <div className="flex flex-1 overflow-hidden relative">
        {/* Sidebar List (always visible on desktop, hidden on mobile if map view) */}
        <div className={`w-full lg:w-[400px] xl:w-[450px] bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0 ${
          viewMode === 'map' ? 'hidden lg:block' : 'block'
        }`}>
          <div className="p-4">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-lg font-bold text-charcoal">
                {places.length} places found
              </h2>
              <button 
                onClick={() => navigateTo('add-spot')}
                className="flex items-center text-sm font-medium text-primary bg-cream px-3 py-1.5 rounded-lg hover:bg-orange-100 transition-colors"
              >
                <Plus size={16} className="mr-1" /> Add Spot
              </button>
            </div>
            
            <div className="flex flex-col gap-4">
              {places.map((place) => (
                <div 
                  key={place.id}
                  onClick={() => navigateTo('detail', place)}
                  className="flex gap-4 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors border border-transparent hover:border-gray-100 group"
                >
                  <div className="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                    <img 
                      src={place.image} 
                      alt={place.name} 
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform"
                    />
                  </div>
                  <div className="flex-1 min-w-0 py-1">
                    <div className="flex justify-between items-start mb-1">
                      <h3 className="font-bold text-charcoal truncate pr-2">{place.name}</h3>
                      <div className="flex items-center text-sm font-bold bg-cream px-1.5 py-0.5 rounded text-primary">
                        <Star size={12} className="fill-primary mr-1" />
                        {place.rating}
                      </div>
                    </div>
                    <p className="text-sm text-gray-500 mb-1">{place.category} • {place.priceRange}</p>
                    <div className="flex items-center text-xs text-gray-500 mt-2">
                      <MapPin size={12} className="mr-1" />
                      <span className="truncate">{place.location}</span>
                      <span className="mx-2">•</span>
                      <span>{place.distance}</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Map Area */}
        <div className={`flex-1 bg-gray-200 relative ${
          viewMode === 'list' ? 'hidden lg:block' : 'block'
        }`}>
          {/* Placeholder for actual map integration (Google Maps, Mapbox, etc.) */}
          <div className="absolute inset-0 bg-[#e5e3df]">
            <img 
              src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=1920&q=80" 
              alt="Map placeholder" 
              className="w-full h-full object-cover opacity-40 grayscale"
            />
            
            {/* Mock Map Pins */}
            <div className="absolute top-1/4 left-1/4 transform -translate-x-1/2 -translate-y-1/2">
              <div className="relative group cursor-pointer" onClick={() => navigateTo('detail', places[0])}>
                <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white z-10 relative">
                  <Utensils size={20} />
                </div>
                <div className="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 bg-white px-3 py-1.5 rounded-lg shadow-xl text-sm font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                  {places[0]?.name}
                </div>
              </div>
            </div>
            
            <div className="absolute top-1/2 left-2/3 transform -translate-x-1/2 -translate-y-1/2">
              <div className="relative group cursor-pointer" onClick={() => navigateTo('detail', places[1])}>
                <div className="w-10 h-10 bg-charcoal rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white z-10 relative">
                  <Pizza size={20} />
                </div>
                <div className="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 bg-white px-3 py-1.5 rounded-lg shadow-xl text-sm font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                  {places[1]?.name}
                </div>
              </div>
            </div>

            <div className="absolute bottom-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
              <div className="relative group cursor-pointer" onClick={() => navigateTo('detail', places[2])}>
                <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white shadow-lg border-2 border-white z-10 relative">
                  <Coffee size={20} />
                </div>
                <div className="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 bg-white px-3 py-1.5 rounded-lg shadow-xl text-sm font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                  {places[2]?.name}
                </div>
              </div>
            </div>
          </div>
          
          {/* Map Controls */}
          <div className="absolute bottom-6 right-6 flex flex-col gap-2">
            <button className="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-charcoal hover:bg-gray-50">
              <span className="text-xl font-bold leading-none">+</span>
            </button>
            <button className="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-charcoal hover:bg-gray-50">
              <span className="text-xl font-bold leading-none">-</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
