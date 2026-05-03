import React, { useState } from 'react';
import { User, Heart, MessageSquare, Settings, LogOut, MapPin, Star, ChevronRight, Search, Edit2, Trash2, PlusCircle } from 'lucide-react';

export default function ProfilePage({ navigateTo, places }: { navigateTo: (page: string, data?: any) => void, places: any[] }) {
  const [activeTab, setActiveTab] = useState('saved');

  const savedPlaces = places.slice(0, 3);
  const reviewedPlaces = places.slice(3, 5);

  return (
    <div className="bg-gray-50 min-h-[calc(100vh-64px)] pb-12">
      {/* Profile Header */}
      <div className="bg-white border-b border-gray-200 pt-12 pb-8 px-4 sm:px-6 lg:px-8">
        <div className="max-w-4xl mx-auto flex flex-col sm:flex-row items-center sm:items-start gap-6">
          <div className="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-white shadow-lg flex-shrink-0">
            <img 
              src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80" 
              alt="User Profile" 
              className="w-full h-full object-cover"
            />
          </div>
          <div className="flex-1 text-center sm:text-left">
            <h1 className="text-3xl font-bold text-charcoal mb-2">Jessica Chen</h1>
            <p className="text-gray-500 flex items-center justify-center sm:justify-start mb-4">
              <MapPin size={16} className="mr-1" /> San Francisco, CA
            </p>
            <div className="flex flex-wrap justify-center sm:justify-start gap-4 text-sm">
              <div className="bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                <span className="font-bold text-charcoal block text-lg">42</span>
                <span className="text-gray-500">Reviews</span>
              </div>
              <div className="bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                <span className="font-bold text-charcoal block text-lg">128</span>
                <span className="text-gray-500">Saved Places</span>
              </div>
              <div className="bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                <span className="font-bold text-charcoal block text-lg">15</span>
                <span className="text-gray-500">Cities Explored</span>
              </div>
            </div>
          </div>
          <div className="flex flex-col gap-2 w-full sm:w-auto">
            <button 
              onClick={() => navigateTo('add-spot')}
              className="bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-xl font-medium transition-colors flex items-center justify-center"
            >
              <PlusCircle size={18} className="mr-2" /> Add a Spot
            </button>
            <button className="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-medium transition-colors flex items-center justify-center">
              <Settings size={18} className="mr-2" /> Edit Profile
            </button>
          </div>
        </div>
      </div>

      {/* Profile Content */}
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        {/* Tabs */}
        <div className="flex border-b border-gray-200 mb-8 overflow-x-auto hide-scrollbar">
          <button 
            onClick={() => setActiveTab('saved')}
            className={`flex items-center gap-2 px-6 py-4 font-medium text-sm whitespace-nowrap border-b-2 transition-colors ${
              activeTab === 'saved' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-800'
            }`}
          >
            <Heart size={18} /> Saved Places
          </button>
          <button 
            onClick={() => setActiveTab('reviews')}
            className={`flex items-center gap-2 px-6 py-4 font-medium text-sm whitespace-nowrap border-b-2 transition-colors ${
              activeTab === 'reviews' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-800'
            }`}
          >
            <MessageSquare size={18} /> My Reviews
          </button>
        </div>

        {/* Tab Content */}
        {activeTab === 'saved' && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {savedPlaces.map((place) => (
              <div 
                key={place.id} 
                onClick={() => navigateTo('detail', place)}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow cursor-pointer border border-gray-100 group flex flex-col"
              >
                <div className="relative h-40 overflow-hidden flex-shrink-0">
                  <img 
                    src={place.image} 
                    alt={place.name} 
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                  <div className="absolute top-3 right-3 bg-white/90 backdrop-blur-sm p-1.5 rounded-full text-primary shadow-md">
                    <Heart size={16} className="fill-primary" />
                  </div>
                </div>
                <div className="p-4 flex-1 flex flex-col">
                  <h3 className="text-lg font-bold text-charcoal truncate mb-1">{place.name}</h3>
                  <div className="flex items-center text-sm text-gray-500 mb-2">
                    <Star size={14} className="text-yellow-400 fill-yellow-400 mr-1" />
                    <span className="font-bold text-charcoal mr-1">{place.rating}</span>
                    <span>• {place.category}</span>
                  </div>
                  <div className="flex items-center text-xs text-gray-500 mt-auto">
                    <MapPin size={12} className="mr-1 text-primary" />
                    <span className="truncate">{place.location}</span>
                  </div>
                </div>
              </div>
            ))}
            
            {/* Find More Card */}
            <div 
              onClick={() => navigateTo('explore')}
              className="bg-cream/50 rounded-2xl border-2 border-dashed border-primary/30 flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-cream transition-colors min-h-[220px]"
            >
              <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center text-primary shadow-sm mb-3">
                <Search size={20} />
              </div>
              <h3 className="font-bold text-charcoal mb-1">Discover More</h3>
              <p className="text-sm text-gray-500 text-center">Find new places to save to your collection</p>
            </div>
          </div>
        )}

        {activeTab === 'reviews' && (
          <div className="space-y-6">
            {reviewedPlaces.map((place) => (
              <div key={place.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row gap-6">
                <div 
                  className="w-full sm:w-32 h-32 rounded-xl overflow-hidden flex-shrink-0 cursor-pointer group"
                  onClick={() => navigateTo('detail', place)}
                >
                  <img src={place.image} alt={place.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                </div>
                <div className="flex-1">
                  <div className="flex justify-between items-start mb-2">
                    <h3 
                      className="text-lg font-bold text-charcoal hover:text-primary cursor-pointer transition-colors"
                      onClick={() => navigateTo('detail', place)}
                    >
                      {place.name}
                    </h3>
                    <span className="text-sm text-gray-400">Oct 12, 2023</span>
                  </div>
                  <div className="flex mb-3">
                    {[...Array(5)].map((_, i) => (
                      <Star key={i} size={16} className={i < Math.floor(place.rating) ? "text-yellow-400 fill-yellow-400" : "text-gray-300"} />
                    ))}
                  </div>
                  <p className="text-gray-600 text-sm leading-relaxed mb-4">
                    "This place is an absolute gem! The atmosphere is perfect and the food exceeded all my expectations. I highly recommend trying their signature dishes. The staff was also incredibly friendly and attentive."
                  </p>
                  <div className="flex gap-3">
                    <button className="text-sm font-medium text-gray-500 hover:text-charcoal transition-colors flex items-center">
                      <Edit2 size={14} className="mr-1" /> Edit
                    </button>
                    <button className="text-sm font-medium text-red-500 hover:text-red-600 transition-colors flex items-center">
                      <Trash2 size={14} className="mr-1" /> Delete
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
