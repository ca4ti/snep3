< ? p h p 
 / * * 
   *     T h i s   f i l e   i s   p a r t   o f   S N E P . 
   *     P a r a   t e r r i t ��r i o   B r a s i l e i r o   l e i a   L I C E N C A _ B R . t x t 
   *     A l l   o t h e r   c o u n t r i e s   r e a d   t h e   f o l l o w i n g   d i s c l a i m e r 
   * 
   *     S N E P   i s   f r e e   s o f t w a r e :   y o u   c a n   r e d i s t r i b u t e   i t   a n d / o r   m o d i f y 
   *     i t   u n d e r   t h e   t e r m s   o f   t h e   G N U   G e n e r a l   P u b l i c   L i c e n s e   a s   p u b l i s h e d   b y 
   *     t h e   F r e e   S o f t w a r e   F o u n d a t i o n ,   e i t h e r   v e r s i o n   3   o f   t h e   L i c e n s e ,   o r 
   *     ( a t   y o u r   o p t i o n )   a n y   l a t e r   v e r s i o n . 
   * 
   *     S N E P   i s   d i s t r i b u t e d   i n   t h e   h o p e   t h a t   i t   w i l l   b e   u s e f u l , 
   *     b u t   W I T H O U T   A N Y   W A R R A N T Y ;   w i t h o u t   e v e n   t h e   i m p l i e d   w a r r a n t y   o f 
   *     M E R C H A N T A B I L I T Y   o r   F I T N E S S   F O R   A   P A R T I C U L A R   P U R P O S E .     S e e   t h e 
   *     G N U   G e n e r a l   P u b l i c   L i c e n s e   f o r   m o r e   d e t a i l s . 
   * 
   *     Y o u   s h o u l d   h a v e   r e c e i v e d   a   c o p y   o f   t h e   G N U   G e n e r a l   P u b l i c   L i c e n s e 
   *     a l o n g   w i t h   S N E P .     I f   n o t ,   s e e   < h t t p : / / w w w . g n u . o r g / l i c e n s e s / > . 
   * / 
 
 $ i d   =   $ _ G E T [ ' i d ' ] ; 
 $ f i l e   =   $ _ G E T [ ' f i l e ' ] ; 
 
 i f ( ! i s s e t ( $ i d )   & &   ! i s s e t ( $ f i l e ) ) { 
 	 e c h o   " { ' s t a t u s ' : ' e r r o r ' , ' m e s s a g e ' : ' E s p e r a n d o   i d   c o m o   a r g u m e n t o ! ' } " ; 
 	 e x i t ( 1 ) ; 
 } e l s e i f ( i s s e t ( $ i d ) ) { 
 	 $ e x p _ d a t a   =   p r e g _ m a t c h ( " / ^ [ 0 - 9 ] + _ ( [ 0 - 9 ] + ) _ / " , $ i d , $ d a t a ) ; 
 	 $ e x p _ d a t a   =   $ d a t a [ 1 ] ; 
 	 $ p a t t e r n   =   ' / ( 2 0 [ 1 2 ] [ 0 - 9 ] ) ( [ 0 - 9 ] [ 0 - 9 ] ) ( [ 0 - 9 ] [ 0 - 9 ] ) / ' ; 
 	 $ r e p l a c e m e n t   =   ' $ 1 - $ 2 - $ 3 ' ; 
 
 	 / / c l i c k t o c a l l 
 	 i f ( $ d a t a   = =   " " ) { 
 	 	 $ d a t a   =   s u b s t r ( $ i d , 0 , 4 )   .   " - " . s u b s t r ( $ i d , 4 , 2 ) . " - " . s u b s t r ( $ i d , 6 , 2 ) ; 
 	 } 
 
 	 $ d a t a   =   p r e g _ r e p l a c e ( $ p a t t e r n , $ r e p l a c e m e n t , $ e x p _ d a t a ) ; 
 	 i f ( f i l e _ e x i s t s ( " $ i d . W A V " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ i d . W A V " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " $ i d . w a v " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ i d . w a v " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ i d . W A V " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ i d . W A V " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ i d . w a v " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ i d . w a v " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " $ d a t a / $ i d . W A V " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ d a t a / $ i d . W A V " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " $ d a t a / $ i d . w a v " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ d a t a / $ i d . w a v " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ d a t a / $ i d . W A V " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ d a t a / $ i d . W A V " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ d a t a / $ i d . w a v " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ d a t a / $ i d . w a v " ) ; 
 	 } e l s e { 
 	 	 e c h o   " { ' s t a t u s ' : ' e r r o r ' , ' m e s s a g e ' : ' F i l e   $ i d   n o t   f o u n d ' , ' d a t e ' : ' $ d a t a ' } " ; 
 	 } 
 
 } e l s e i f ( i s s e t ( $ f i l e ) ) { 
 	 $ e x p _ d a t a   =   p r e g _ m a t c h ( " / ^ [ 0 - 9 ] + _ ( [ 0 - 9 ] + ) _ / " , $ f i l e , $ d a t a ) ; 
 	 $ e x p _ d a t a   =   $ d a t a [ 1 ] ; 
 	 $ p a t t e r n   =   ' / ( 2 0 [ 1 2 ] [ 0 - 9 ] ) ( [ 0 - 9 ] [ 0 - 9 ] ) ( [ 0 - 9 ] [ 0 - 9 ] ) / ' ; 
 	 $ r e p l a c e m e n t   =   ' $ 1 - $ 2 - $ 3 ' ; 
 
 	 / / c l i c k t o c a l l 
 	 i f ( $ d a t a   = =   " " ) { 
 	 	 $ d a t a   =   s u b s t r ( $ i d , 0 , 4 )   .   " - " . s u b s t r ( $ i d , 4 , 2 ) . " - " . s u b s t r ( $ i d , 6 , 2 ) ; 
 	 } 
 	 
 	 $ d a t a   =   p r e g _ r e p l a c e ( $ p a t t e r n , $ r e p l a c e m e n t , $ e x p _ d a t a ) ; 
 	 i f ( f i l e _ e x i s t s ( " $ f i l e " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ f i l e " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ f i l e " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ f i l e " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " $ d a t a / $ f i l e " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   $ d a t a / $ f i l e " ) ; 
 	 } e l s e i f ( f i l e _ e x i s t s ( " s t o r a g e 1 / $ d a t a / $ f i l e " ) ) { 
 	 	 h e a d e r ( " L o c a t i o n :   s t o r a g e 1 / $ d a t a / $ f i l e " ) ; 
 	 } e l s e { 
 	 	 e c h o   " { ' s t a t u s ' : ' e r r o r ' , ' m e s s a g e ' : ' F i l e   $ f i l e   n o t   f o u n d ' , ' d a t e ' : ' $ d a t a ' } " ; 
 	 } 
 
 } 
 