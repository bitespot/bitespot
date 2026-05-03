import React from 'react';
import { ArrowLeft, Star, MapPin, Share2, Heart, MessageSquare, Clock, Phone, Globe } from 'lucide-react';

export default function DetailPage({ place, navigateTo }) {
  if (!place) {
    return <div className="p-8 text-center">Place not found. <button onClick={() => navigateTo('home')} className="text-primary underline">Go back</button></div>;
  }

  const menuItems = [
    { id: 1, name: 'Signature Dish', description: 'Our chef\'s special creation with secret sauce', price: '$18.50', image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80' },
    { id: 2, name: 'Spicy Noodles', description: 'Wok-tossed noodles with chili and fresh vegetables', price: '$14.00', image: 'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=400&q=80' },
    { id: 3, name: 'Classic Burger', description: 'Double patty with cheese, lettuce, and tomato', price: '$16.00', image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=400&q=80' },
    { id: 4, name: 'Fresh Salad', description: 'Mixed greens with balsamic vinaigrette', price: '$12.00', image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=400&q=80' },
  ];

  const reviews = [
    { id: 1, user: 'Sarah M.', rating: 5, date: '2 days ago', text: 'Absolutely amazing! The food was delicious and the service was top-notch. Will definitely be coming back.', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80' },
    { id: 2, user: 'David K.', rating: 4, date: '1 week ago', text: 'Great atmosphere and good food. The portions are a bit small for the price, but the quality makes up for it.', avatar: 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&w=100&q=80' },
  ];

  return (
    <div className="bg-gray-50 min-h-screen pb-20">
      {/* Hero Image */}
      <div className="relative h-64 sm:h-80 md:h-96 w-full">
        <img 
          src={place.image} 
          alt={place.name} 
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        
        {/* Top Navigation */}
        <div className="absolute top-0 left-0 right-0 p-4 flex justify-between items-center z-10">
          <button 
            onClick={() => navigateTo('home')}
            className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/40 transition-colors"
          >
            <ArrowLeft size={20} />
          </button>
          <div className="flex gap-2">
            <button className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/40 transition-colors">
              <Share2 size={20} />
            </button>
            <button className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/40 transition-colors">
              <Heart size={20} />
            </button>
          </div>
        </div>

        {/* Title Info */}
        <div className="absolute bottom-0 left-0 right-0 p-6 text-white z-10">
          <div className="flex flex-wrap gap-2 mb-3">
            {place.tags.map((tag, idx) => (
              <span key={idx} className="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-medium">
                {tag}
              </span>
            ))}
          </div>
          <h1 className="text-3xl sm:text-4xl font-bold mb-2">{place.name}</h1>
          <div className="flex flex-wrap items-center gap-4 text-sm sm:text-base">
            <div className="flex items-center">
              <Star size={18} className="text-yellow-400 fill-yellow-400 mr-1" />
              <span className="font-bold mr-1">{place.rating}</span>
              <span className="text-gray-300">({place.reviews} reviews)</span>
            </div>
            <span className="text-gray-300">•</span>
            <span>{place.category}</span>
            <span className="text-gray-300">•</span>
            <span className="font-medium">{place.priceRange}</span>
          </div>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-20">
        <div className="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
          {/* Quick Info */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h2 className="text-xl font-bold text-charcoal mb-4">About</h2>
              <p className="text-gray-600 leading-relaxed">{place.description}</p>
            </div>
            <div className="space-y-4">
              <div className="flex items-start text-gray-600">
                <MapPin size={20} className="mr-3 text-primary flex-shrink-0 mt-0.5" />
                <div>
                  <p className="font-medium text-charcoal">{place.location}</p>
                  <p className="text-sm">{place.distance} away</p>
                </div>
              </div>
              <div className="flex items-center text-gray-600">
                <Clock size={20} className="mr-3 text-primary flex-shrink-0" />
                <p>Open today • 10:00 AM - 10:00 PM</p>
              </div>
              <div className="flex items-center text-gray-600">
                <Phone size={20} className="mr-3 text-primary flex-shrink-0" />
                <p>+1 (555) 123-4567</p>
              </div>
              <div className="flex items-center text-gray-600">
                <Globe size={20} className="mr-3 text-primary flex-shrink-0" />
                <p className="text-primary hover:underline cursor-pointer">Visit website</p>
              </div>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-100">
            <button className="flex-1 bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-bold transition-colors flex items-center justify-center">
              <MapPin size={18} className="mr-2" /> Get Directions
            </button>
            <button className="flex-1 bg-cream text-primary hover:bg-orange-100 py-3 rounded-xl font-bold transition-colors flex items-center justify-center">
              <MessageSquare size={18} className="mr-2" /> Leave a Review
            </button>
          </div>
        </div>

        {/* Menu Section */}
        <div className="mb-10">
          <div className="flex justify-between items-end mb-6">
            <h2 className="text-2xl font-bold text-charcoal">Menu Highlights</h2>
            <button className="text-primary font-medium hover:underline">View full menu</button>
          </div>
          
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {menuItems.map((item) => (
              <div key={item.id} className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-4 hover:shadow-md transition-shadow cursor-pointer group">
                <div className="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                  <img src={item.image} alt={item.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                </div>
                <div className="flex flex-col justify-between flex-1">
                  <div>
                    <div className="flex justify-between items-start mb-1">
                      <h3 className="font-bold text-charcoal">{item.name}</h3>
                      <span className="font-bold text-primary">{item.price}</span>
                    </div>
                    <p className="text-sm text-gray-500 line-clamp-2">{item.description}</p>
                  </div>
                  <button className="text-sm font-medium text-primary self-start mt-2 hover:underline">Add to order</button>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Reviews Section */}
        <div>
          <div className="flex justify-between items-end mb-6">
            <h2 className="text-2xl font-bold text-charcoal">Reviews</h2>
            <div className="flex items-center">
              <Star size={20} className="text-yellow-400 fill-yellow-400 mr-2" />
              <span className="font-bold text-xl mr-1">{place.rating}</span>
              <span className="text-gray-500">({place.reviews})</span>
            </div>
          </div>

          <div className="space-y-4">
            {reviews.map((review) => (
              <div key={review.id} className="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div className="flex justify-between items-start mb-3">
                  <div className="flex items-center gap-3">
                    <img src={review.avatar} alt={review.user} className="w-10 h-10 rounded-full object-cover" />
                    <div>
                      <p className="font-bold text-charcoal">{review.user}</p>
                      <p className="text-xs text-gray-500">{review.date}</p>
                    </div>
                  </div>
                  <div className="flex">
                    {[...Array(5)].map((_, i) => (
                      <Star key={i} size={14} className={i < review.rating ? "text-yellow-400 fill-yellow-400" : "text-gray-300"} />
                    ))}
                  </div>
                </div>
                <p className="text-gray-600 text-sm leading-relaxed">{review.text}</p>
              </div>
            ))}
          </div>
          
          <button className="w-full mt-6 py-3 border border-gray-200 rounded-xl font-medium text-charcoal hover:bg-gray-50 transition-colors">
            Show all {place.reviews} reviews
          </button>
        </div>
      </div>
    </div>
  );
}
