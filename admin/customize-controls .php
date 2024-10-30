<?php
/**
 * JP For TwentyTwentyOne customize control multiple checkbox.
 *
 * @package jp-for-twentytwentyone
 */

/**
 * JP For TwentyTwentyOne customize control multiple checkbox class.
 */
class JP_For_TwentyTwentyOne_Customize_Control_Multiple_Checkbox extends WP_Customize_Control {
	public $type = 'multiple-checkbox';

	protected function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		if ( !empty( $this->label ) ) {
			echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		}

		if ( !empty( $this->description ) ) {
			echo '<span class="description customize-control-description">' . esc_html( $this->description ) . '</span>';
		}

		$multi_values = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();
		echo '<ul>';
		foreach ( $this->choices as $value => $label ) {
			echo '<li>';
			echo '<label>';
			echo '<input type="checkbox" value="' . esc_attr( $value ) . '" ' . checked( in_array( $value, $multi_values ), true, false ) . '>';
			echo esc_html( $label );
			echo '</label>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<input type="hidden" name="mulitiple-checkbox-value" ' . $this->get_link() . ' value="' . esc_attr( implode( ',', $multi_values ) ) . '">'; // WPCS: XSS ok;
	}
}
